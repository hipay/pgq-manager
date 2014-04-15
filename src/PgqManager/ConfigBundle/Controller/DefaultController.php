<?php

namespace PgqManager\ConfigBundle\Controller;

use PgqManager\ConfigBundle\Entity\Settings;
use PgqManager\ConfigBundle\Form\Type\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $new = false;
        $originalDb = array();

        $settings = $this->getDoctrine()->getRepository('ConfigBundle:Settings')->findOneBy(array(
            'uid' => $this->get('security.context')->getToken()->getUsername()
        ));

        if (!$settings) {
            $settings = new Settings();
            $new = true;
        } else {
            foreach ($settings->getDatabases() as $db) $originalDb[] = $db;
        }

        $form = $this->createForm(new SettingsType(), $settings);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $settings->setUid($this->get('security.context')->getToken()->getUsername());

            // filtre $originalTags pour ne contenir que les tags
            // n'étant plus présents
            foreach ($settings->getDatabases() as $db) {
                foreach ($originalDb as $key => $toDel) {
                    if ($toDel->getId() === $db->getId()) {
                        unset($originalDb[$key]);
                    }
                }
            }

            // supprime la relation entre le tag et la « Task »
            foreach ($originalDb as $db) {
                $db->setSettings(null);

                $em->persist($db);

                // si vous souhaitiez supprimer totalement le Tag, vous pourriez
                // aussi faire comme cela
                $em->remove($db);
            }

            $em->persist($settings);

            $em->flush();
        }

        return $this->render('ConfigBundle:Default:index.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
