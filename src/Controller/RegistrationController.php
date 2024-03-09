<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\User1Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twilio\Rest\Client;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function index(Request $request, SluggerInterface $slugger,MailerInterface $mailer)
    {
        $user = new User();

        $form = $this->createForm(User1Type::class, $user);

        $form->handleRequest($request);
       // $this->sendTwilioSms($user->getTel(), 'Congratulations! You have successfully registered in our platform.');

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
   
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
 
                try {
                    $imageFile->move(
                        $this->getParameter('img_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the exception if something happens during the file upload
                }
 
                $user->setImage($newFilename);
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            $user->setRoles(['ROLE_USER']);
            
            $hashedPassword = hash('sha1', $user->getPassword());
            $user->setPassword($hashedPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function sendTwilioSms($recipientNumber, $message)
    {
        // Your Twilio credentials
        $accountSid = 'ACacf742ae2a4773f9ad93081c22e775d8';
        $authToken = 'ca3cd7bb3346341c03661c47b1f9dcda';
        $twilioNumber = '+12162421545';
        $recipientNumber = '+21623565529'; // Replace with the actual recipient's number

        // Create a Twilio client
        $twilio = new Client($accountSid, $authToken);

        // Send the SMS
        $twilio->messages->create(
            $recipientNumber,
            [
                'from' => $twilioNumber,
                'body' => $message,
            ]
        );
    }
}
