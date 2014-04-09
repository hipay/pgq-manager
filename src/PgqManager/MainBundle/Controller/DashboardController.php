<?php

namespace PgqManager\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Intl\Exception\NotImplementedException;

class DashboardController extends Controller
{
    /**
     * @Template()
     * @param $id
     * @throws \Symfony\Component\Intl\Exception\NotImplementedException
     * @return array
     */
    public function indexAction($id)
    {
        throw new NotImplementedException('Coming soon...');
    }
}
