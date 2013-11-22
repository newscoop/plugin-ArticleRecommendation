<?php
/**
 * @package Newscoop\ArticleRecommendationBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticleRecommendationBundle\Controller;

class AdminController extends Controller
{
    /**
    * @Route("/admin/article-recommendation")
    * @Template()
    */
    public function indexAction(Request $request)
    {
        return array();
    }
}