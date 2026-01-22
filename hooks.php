<?php
/**
 * ClientSnapshot Pro - Hooks
 *
 * Extension hooks for future features and integrations.
 *
 * @package    WHMCS
 * @subpackage Addon Modules
 * @author     SKYRHRG Technologies System
 * @version    1.0
 * @copyright  Copyright (c) 2026 SKYRHRG Technologies System
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Hook: Admin Dashboard Widget (Optional)
 * Displays a quick summary widget on the WHMCS admin dashboard
 */
add_hook('AdminHomeWidgets', 1, function () {
    return new class {
        public function getName()
        {
            return 'ClientSnapshot Summary';
        }

        public function getContent()
        {
            // Get quick stats
            $totalClients = Capsule::table('tblclients')->count();
            $activeServices = Capsule::table('tblhosting')
                ->where('domainstatus', 'Active')
                ->count();
            $clientsWithServices = Capsule::table('tblhosting')
                ->where('domainstatus', 'Active')
                ->distinct('userid')
                ->count('userid');

            $html = '<div style="padding: 15px;">';
            $html .= '<div style="display: flex; justify-content: space-between; margin-bottom: 10px;">';
            $html .= '<span><strong>Total Clients:</strong></span>';
            $html .= '<span class="badge badge-primary">' . number_format($totalClients) . '</span>';
            $html .= '</div>';
            $html .= '<div style="display: flex; justify-content: space-between; margin-bottom: 10px;">';
            $html .= '<span><strong>Active Services:</strong></span>';
            $html .= '<span class="badge badge-success">' . number_format($activeServices) . '</span>';
            $html .= '</div>';
            $html .= '<div style="display: flex; justify-content: space-between; margin-bottom: 15px;">';
            $html .= '<span><strong>Clients with Services:</strong></span>';
            $html .= '<span class="badge badge-info">' . number_format($clientsWithServices) . '</span>';
            $html .= '</div>';
            $html .= '<a href="addonmodules.php?module=clientsnapshot" class="btn btn-block btn-default">';
            $html .= '<i class="fas fa-external-link-alt"></i> View Full Report';
            $html .= '</a>';
            $html .= '</div>';

            return $html;
        }
    };
});
