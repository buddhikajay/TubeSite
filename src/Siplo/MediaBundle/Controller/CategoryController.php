<?php

namespace Siplo\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Siplo\MediaBundle\Form\Type\CategoryUploaderType;
use Siplo\MediaBundle\Form\Model\CategoryUpload;

class CategoryController extends Controller
{


    /**
     * @Route("/{country}",requirements={
     *     "country": "^[A-Z]{2}"
     * }))
     *
     */
    public function viewCategoryAction($country)
    {
        $countryEntity=$this->getDoctrine()
            ->getRepository('SiploMediaBundle:Country')->findOneByCode($country);
        $categories = $this->getDoctrine()
            ->getRepository('SiploMediaBundle:Category')->findAll();

        if (!$categories) {
            throw $this->createNotFoundException(
                'No categories found'
            );
        }

        return $this->render('AppBundle::categories.html.twig',array(
            'categories' => $categories,'country'=>$countryEntity));
    }



    /**
     * @Route("/create/category")
     *
     */
    public function createCategoryAction()
    {
        $uploader = new CategoryUpload();
        $form = $this->createForm(new CategoryUploaderType(), $uploader, array(
            'action' => '/create/category/save',
        ));

        return $this->render(
            'SiploMediaBundle::form.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("/create/category/save")
     *
     */
    public function catergorySaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CategoryUploaderType(), new CategoryUpload());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $uploader = $form->getData();

            $em->persist($uploader->getCategory());
            $em->flush();

            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
            $path = $helper->asset($uploader->getCategory(), 'backgroundImage');
//            return $this->redirectToRoute('play');
            return $this->render('SiploMediaBundle::videoplayer.html.twig',array(
                'path' => $path));
        }

        return $this->render(
            'SiploMediaBundle::form.html.twig',
            array('form' => $form->createView())
        );
    }
}
