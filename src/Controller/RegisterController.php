<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Account;
use App\Repository\AccountRepository;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\UtilsService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly UtilsService $utils,
    ){}

    #[Route('/register', name: 'app_register_addaccount')]
    public function addAccount(Request $request, ValidatorInterface $validator): Response
    {
        $msg = "";
        $type = "";
        //Crée un objet Account
        $account = new Account();
        //Crée un objet RegisterType(formulaire)
        $form = $this->createForm(RegisterType::class, $account);
        //Récupère le résultat de la requête
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            //Si l'entité est valide (validation)
            $errors = $validator->validate($account);
            if(count($errors) > 0){
                $msg = $errors[0]->getMessage();
                $type = "warning";
            }
            //Sinon on ajoute en BDD
            else {
                 //Teste si le compte n'existe pas
                if(!$this->accountRepository->findOneBy(["email" => $account->getEmail()])) {
                    $account->setRoles(["ROLE_USER"]);
                    $account->setStatus(false);
                    $this->em->persist($account);
                    $this->em->flush();
                    $msg = "Le compte a été ajouté en BDD";
                    $type = "success";
                }
                else {
                    $msg = "Les informations email et/ou password existent déjà";
                    $type = "danger";
                }
            }
           
            $this->addFlash($type, $msg);
        }

        return $this->render('register/addaccount.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/activate/{id}', name: 'app_register_activate')]
    public function activate(mixed $id): Response {
        try {
            $id = $this->utils->decodeBase64($id);
            if(is_numeric($id)) {
                $account = $this->accountRepository->find($id);
                if(!$account->isStatus()) {
                    $account->setStatus(true);
                    $this->em->persist($account);
                    $this->em->flush();
                }
            }
        } catch (\Exception $e) {
            $this->addFlash("warning", $e->getMessage());
        }
        
        return $this->redirectToRoute('app_login');
    }
}
