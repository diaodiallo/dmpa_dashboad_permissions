<?php
/**
 * Created by PhpStorm.
 * User: ddiallo
 * Date: 11/15/18
 * Time: 1:48 PM
 */

namespace Drupal\dmpa_dashboard_permissions\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Url;
use \Drupal\Core\Session\AccountProxyInterface;

/**
 * Control the access of country pages.
 */
class DmpaDashboardPermissionsSubscriber implements EventSubscriberInterface {


    /**
     * Redirect pattern based url
     * @param GetResponseEvent $event
     */
    public function controlAccess(GetResponseEvent $event) {

        //Get the current url and put arguments in array
        $request = \Drupal::request();
        $requestUrl = $request->server->get('REQUEST_URI', null);

        if ($requestUrl !== '/') {

        $urlArgs = explode('/', $requestUrl);

        //Get the current user, his roles and subscribed countries
        $currentUser = \Drupal::currentUser();
        $roles = $currentUser->getRoles();
       $currentUserCountries = getUserCountries($currentUser->id());

        /**
         * Exclude access control for some roles
         * 'administrator' 'dpma_sc_team' 'ops_and_donors' 'shareable_view'
         */

        if (!in_array('administrator', $roles) && !in_array('ops_and_donors', $roles) && !in_array('dpma_sc_team', $roles) && !in_array('shareable_view', $roles)){

            if (sizeof($urlArgs)==2){

                /**
                 * Get the country id from terms
                 */

                $termArray = \Drupal::entityTypeManager()
                    ->getStorage('taxonomy_term')
                    ->loadByProperties(['name' => $urlArgs[1]]);
                $countryId = reset($termArray);
                $idForLanding = $countryId->id();

                $status = (in_array($idForLanding, $currentUserCountries));
                if (!$status){
                    throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
                }

            }elseif (sizeof($urlArgs)==5 && ($urlArgs[1]=='countries' || $urlArgs[1]=='process-indicators')){

                /**
                 * Get country id from terms
                 */

                $termArray = \Drupal::entityTypeManager()
                    ->getStorage('taxonomy_term')
                    ->loadByProperties(['name' => $urlArgs[2]]);
                $countryId = reset($termArray);
                $idForProcess = $countryId->id();

                $status = (in_array($idForProcess, $currentUserCountries));
                if (!$status){
                    throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
                }
            }elseif (sizeof($urlArgs)==3 && $urlArgs[1]=='training-data') {
                $termArray = \Drupal::entityTypeManager()
                    ->getStorage('taxonomy_term')
                    ->loadByProperties(['name' => $urlArgs[2]]);
                $countryId = reset($termArray);
                $idForProcess = $countryId->id();

                $status = (in_array($idForProcess, $currentUserCountries));
                if (!$status){
                    throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
                }
            }

        }
        }
    }

    /**
     * Listen to kernel.request events and call customRedirection.
     * {@inheritdoc}
     * @return array Event names to listen to (key) and methods to call (value)
     */
    public static function getSubscribedEvents() {
        $events[KernelEvents::REQUEST][] = array('controlAccess');
        return $events;
    }



    /**
     * @param $userId
     * logged in user id
     *
     * @return array of country ids logged in user is associated with.
     */
    function getUserCountries($userId) {
        $user = \Drupal\user\Entity\User::load($userId);
        $userCountries = $user->field_user_country->getValue();
       $countryIds = [];
       foreach ($userCountries as $userCountry) {
           array_push($countryIds, $userCountry['target_id']);
       }

        return $countryIds;
    }
}