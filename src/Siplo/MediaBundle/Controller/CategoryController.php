<?php

namespace Siplo\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Siplo\MediaBundle\Form\Type\CategoryType;
use Siplo\MediaBundle\Entity\Category;

class CategoryController extends Controller
{

//country is a integer, hence regx used
    /**
     * @Route("/{country}",requirements={
     *     "country": "(?<![-.])\b[0-9]+\b(?!\.[0-9])"
     * }))
     *
     */

    public function viewCategoryAction($country)
    {
        $countryEntity=$this->getDoctrine()
            ->getRepository('SiploMediaBundle:Country')->findOneById($country);
        $categories = $this->getDoctrine()
            ->getRepository('SiploMediaBundle:Category')->findAll();

        if (!$categories) {
            throw $this->createNotFoundException(
                'No categories found'
            );
        }

        $videos = $this->getDoctrine()
            ->getRepository('SiploMediaBundle:Video')->findBy(
                array('authorised'=>true,'country'=>$countryEntity->getId()),
                array('rating' => 'DESC')
            );
        if (!$videos) {
            return $this->render('AppBundle::emptycontent.html.twig'
            );
        }
        return $this->render('AppBundle::categories.html.twig',array(
            'categories' => $categories,'country'=>$countryEntity,'videos'=>$videos));
    }



    /**
     * @Route("/create/category")
     *
     */
    public function createAction(Request $request)
    {

        $form = $this->createForm(new CategoryType(), new Category());
        $form->handleRequest($request);
        if ($form->isValid()) {


            $category = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->render('SiploMediaBundle::upload_successful.html.twig');

        }

        return $this->render(
            '@SiploMedia/form.html.twig',
            array('form' => $form->createView())
        );

    }
}
