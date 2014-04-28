<?php

namespace PgqManager\ConfigBundle\Controller;

use Braincrafted\Bundle\BootstrapBundle\Session\FlashMessage;
use Doctrine\Common\Collections\ArrayCollection;
use PgqManager\ConfigBundle\Entity\Database;
use PgqManager\ConfigBundle\Entity\Settings;
use PgqManager\ConfigBundle\Form\Type\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\DumpException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $new = false;

        $originalDb = $this->loadFromConfig(); //$this->getDoctrine()->getRepository('ConfigBundle:Database')->findAll;

        $settings = new Settings();
        $settings->setDatabases($originalDb);

        $form = $this->createForm(new SettingsType(), $settings);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $iter = 0;
            foreach($settings->getDatabases() as $database) {
                $database->setId($iter++);
            }
            $this->saveConfig($settings);
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('Modifications applied');
        }

        return $this->render('ConfigBundle:Default:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function loadFromConfig()
    {
        $dbmanager = $this->get('pgq_config_bundle.db_manager');
        $result = new ArrayCollection();

        try {
            if ($dbmanager->hasDatabases()) {
                $result = $dbmanager->getDatabases();
            }

        } catch (ParseException $e) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error(sprintf("Unable to parse the YAML string: %s", $e->getMessage()));
        }
        return $result;
    }

    private function saveConfig(Settings $settings)
    {
        $yaml = new Dumper();

        try {
            $content = $yaml->dump($settings->toArray(), 2);
            $dir = realpath(__DIR__ . '/../Resources/config');

            if (is_writable($dir)) {
                file_put_contents($dir . '/databases.yml', $content);
            } else {
                throw new \Exception(sprintf('File "%s" is not writable.', $dir));
            }

        } catch (DumpException $e) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error(sprintf("Unable to dump the array to YAML format : %s", $e->getMessage()));

        } catch (\Exception $e) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->error($e->getMessage());
        }
    }
}
