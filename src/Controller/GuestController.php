<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Repository\GuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/guest')]
final class GuestController extends AbstractController
{
    #[Route(name: 'app_guest_index', methods: ['GET'])]
    public function index(GuestRepository $guestRepository, SerializerInterface $serializer): Response
    {
        $guests = $guestRepository->findAll();
        $guestsJson = $serializer->serialize($guests, 'json'); 
        return new Response($guestsJson, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/new', name: 'app_guest_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);

        $guest = new Guest();
        $guest->setName($data['name'] ?? '');
        $guest->setSurname($data['surname'] ?? '');
        $guest->setEmail($data['email'] ?? '');
        $guest->setPhone($data['phone'] ?? ''); 

        $entityManager->persist($guest);
        $entityManager->flush();

        $guestJson = $serializer->serialize($guest, 'json');
        return new Response($guestJson, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'app_guest_show', methods: ['GET'])]
    public function show(Guest $guest, SerializerInterface $serializer): Response
    {
        $guestJson = $serializer->serialize($guest, 'json');
        return new Response($guestJson, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}/edit', name: 'app_guest_edit', methods: ['PATCH'])] 
    public function edit(Request $request, Guest $guest, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);
    
        if (isset($data['name'])) {
            $guest->setName($data['name']);
        }
        if (isset($data['surname'])) {
            $guest->setSurname($data['surname']);
        }
        if (isset($data['email'])) {
            $guest->setEmail($data['email']);
        }
        if (isset($data['phone'])) {
            $guest->setPhone($data['phone']);
        }
    
        $entityManager->flush();
    
        $guestJson = $serializer->serialize($guest, 'json');
        return new Response($guestJson, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'app_guest_delete', methods: ['DELETE'])]
    public function delete(Guest $guest, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($guest);
        $entityManager->flush();

        return new Response(
            json_encode(['message' => 'Guest deleted successfully.']),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
