<?php

namespace App\Controller;

use App\Service\CartService;
use Stripe\StripeClient;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/checkout')]
class StripeController extends AbstractController
{
    public function __construct(
        private readonly StripeClient $stripe,
        private readonly CartService $cartService,
        private readonly string $stripeWebhookSecret
    ) {}

    /**
     * Affiche la page de paiement avec le récapitulatif du vrai panier
     */
    #[Route('', name: 'app_checkout', methods: ['GET'])]
    public function index(): Response
    {
        $panier = $this->cartService->getCartItems();

        if (empty($panier)) {
            $this->addFlash('info', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        return $this->render('stripe/checkout.html.twig', [
            'panier' => $panier,
            'total'  => $this->cartService->getTotal($panier),
        ]);
    }

    /**
     * Crée une Checkout Session Stripe depuis le vrai panier et redirige vers Stripe
     */
    #[Route('', name: 'app_checkout_post', methods: ['POST'])]
    public function checkout(): Response
    {
        $panier = $this->cartService->getCartItems();

        if (empty($panier)) {
            $this->addFlash('info', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        // Construire les line_items depuis les articles réels du panier
        $lineItems = [];
        foreach ($panier as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'unit_amount'  => (int) round($item->getFleur()->getPrix() * 100), // centimes
                    'product_data' => [
                        'name'   => $item->getFleur()->getNom(),
                        'images' => $item->getFleur()->getImageName()
                            ? [$this->generateUrl('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL) . 'uploads/fleurs/' . $item->getFleur()->getImageName()]
                            : [],
                    ],
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items'  => $lineItems,
            'mode'        => 'payment',
            'success_url' => $this->generateUrl('app_checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'  => $this->generateUrl('app_checkout_cancel',  [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        // Redirection 303 (See Other) vers Stripe — obligatoire après un POST
        return $this->redirect($session->url, 303);
    }

    /**
     * Page affichée après un paiement réussi — vide le panier
     */
    #[Route('/success', name: 'app_checkout_success', methods: ['GET'])]
    public function success(): Response
    {
        $this->cartService->clearCart();
        return $this->render('stripe/success.html.twig');
    }

    /**
     * Page affichée si l'utilisateur annule le paiement sur Stripe
     */
    #[Route('/cancel', name: 'app_checkout_cancel', methods: ['GET'])]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }

    /**
     * Webhook Stripe — reçoit les événements POST de Stripe.
     * Route publique (pas d'authentification Symfony requise).
     * La signature HMAC est vérifiée via le webhook secret.
     */
    #[Route('/webhook', name: 'app_stripe_webhook', methods: ['POST'])]
    public function webhook(Request $request): Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->stripeWebhookSecret
            );
        } catch (SignatureVerificationException $e) {
            // Signature invalide — Stripe ou requête tierce malformée
            return new Response('Signature invalide', 400);
        } catch (\UnexpectedValueException $e) {
            // Payload invalide
            return new Response('Payload invalide', 400);
        }

        // Traitement des événements Stripe
        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            default => null, // Ignorer les autres événements
        };

        return new Response('OK', 200);
    }

    /**
     * Traite un paiement Checkout complété.
     * Ici : log simple. En production, marquer la commande comme payée en base.
     */
    private function handleCheckoutCompleted(object $session): void
    {
        // $session->payment_intent  → ID du PaymentIntent
        // $session->customer_email  → email du client (si fourni)
        // $session->amount_total    → montant total en centimes
        // TODO : mettre à jour la commande en base de données
    }
}
