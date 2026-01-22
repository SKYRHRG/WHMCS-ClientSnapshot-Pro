<?php
/**
 * ClientSnapshot Pro - WHMCS Addon Module
 *
 * Displays client data with name, phone number, and active service count.
 * Features CSV/Excel export capability for client reporting.
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
 * Module Configuration
 *
 * @return array Module configuration values
 */
function clientsnapshot_config()
{
    return [
        'name' => 'ClientSnapshot Pro',
        'description' => 'Display client data with name, phone number, and active services count. Export to CSV/Excel.',
        'author' => 'SKYRHRG Technologies System',
        'language' => 'english',
        'version' => '1.0',
        'fields' => [
            'items_per_page' => [
                'FriendlyName' => 'Items Per Page',
                'Type' => 'dropdown',
                'Options' => '10,25,50,100',
                'Default' => '25',
                'Description' => 'Number of clients to display per page in the dashboard.',
            ],
            'show_inactive_clients' => [
                'FriendlyName' => 'Show Inactive Clients',
                'Type' => 'yesno',
                'Default' => 'yes',
                'Description' => 'Include clients with zero active services in the list.',
            ],
        ]
    ];
}

/**
 * Module Activation
 *
 * @return array Status and description
 */
function clientsnapshot_activate()
{
    return [
        'status' => 'success',
        'description' => 'ClientSnapshot Pro has been activated successfully.'
    ];
}

/**
 * Module Deactivation
 *
 * @return array Status and description
 */
function clientsnapshot_deactivate()
{
    return [
        'status' => 'success',
        'description' => 'ClientSnapshot Pro has been deactivated successfully.'
    ];
}

/**
 * Module Upgrade
 *
 * @param array $vars Module variables
 * @return void
 */
function clientsnapshot_upgrade($vars)
{
    $currentVersion = $vars['version'];
    // Future upgrade logic here
}

/**
 * Admin Area Output
 *
 * @param array $vars Module configuration variables
 * @return void
 */
function clientsnapshot_output($vars)
{
    // Get module settings
    $itemsPerPage = isset($vars['items_per_page']) ? (int) $vars['items_per_page'] : 25;
    $showInactive = isset($vars['show_inactive_clients']) && $vars['show_inactive_clients'] === 'on';

    // Handle export requests
    if (isset($_GET['export']) && in_array($_GET['export'], ['csv', 'excel'])) {
        clientsnapshot_export($_GET['export']);
        return;
    }

    // Fetch client data with active service counts
    $clients = clientsnapshot_get_clients($showInactive);

    // Calculate statistics
    $totalClients = count($clients);
    $totalActiveServices = 0;
    $clientsWithServices = 0;

    foreach ($clients as $client) {
        $totalActiveServices += $client->active_services;
        if ($client->active_services > 0) {
            $clientsWithServices++;
        }
    }

    // Get module path for assets
    $moduleUrl = '../modules/addons/clientsnapshot/';

    // Include custom CSS
    echo '<link rel="stylesheet" href="' . $moduleUrl . 'assets/css/style.css">';

    // Dashboard Header
    echo '<div class="clientsnapshot-container">';

    // Module Header
    echo '<div class="cs-header">';
    echo '<div class="cs-header-content">';
    echo '<h2><i class="fas fa-users"></i> ClientSnapshot Pro</h2>';
    echo '<p class="cs-subtitle">Client Overview & Service Statistics</p>';
    echo '</div>';
    echo '<div class="cs-header-actions">';
    echo '<a href="?module=clientsnapshot&export=csv" class="btn btn-success"><i class="fas fa-file-csv"></i> Export CSV</a>';
    echo '<a href="?module=clientsnapshot&export=excel" class="btn btn-primary"><i class="fas fa-file-excel"></i> Export Excel</a>';
    echo '</div>';
    echo '</div>';

    // Statistics Cards
    echo '<div class="cs-stats-row">';

    echo '<div class="cs-stat-card cs-stat-primary">';
    echo '<div class="cs-stat-icon"><i class="fas fa-users"></i></div>';
    echo '<div class="cs-stat-content">';
    echo '<span class="cs-stat-value">' . number_format($totalClients) . '</span>';
    echo '<span class="cs-stat-label">Total Clients</span>';
    echo '</div>';
    echo '</div>';

    echo '<div class="cs-stat-card cs-stat-success">';
    echo '<div class="cs-stat-icon"><i class="fas fa-server"></i></div>';
    echo '<div class="cs-stat-content">';
    echo '<span class="cs-stat-value">' . number_format($totalActiveServices) . '</span>';
    echo '<span class="cs-stat-label">Active Services</span>';
    echo '</div>';
    echo '</div>';

    echo '<div class="cs-stat-card cs-stat-info">';
    echo '<div class="cs-stat-icon"><i class="fas fa-user-check"></i></div>';
    echo '<div class="cs-stat-content">';
    echo '<span class="cs-stat-value">' . number_format($clientsWithServices) . '</span>';
    echo '<span class="cs-stat-label">Clients with Services</span>';
    echo '</div>';
    echo '</div>';

    echo '<div class="cs-stat-card cs-stat-warning">';
    echo '<div class="cs-stat-icon"><i class="fas fa-chart-line"></i></div>';
    echo '<div class="cs-stat-content">';
    $avgServices = $totalClients > 0 ? round($totalActiveServices / $totalClients, 2) : 0;
    echo '<span class="cs-stat-value">' . $avgServices . '</span>';
    echo '<span class="cs-stat-label">Avg Services/Client</span>';
    echo '</div>';
    echo '</div>';

    echo '</div>'; // End stats row

    // Data Table
    echo '<div class="cs-table-container">';
    echo '<table id="clientsnapshot-table" class="table table-striped table-hover cs-datatable">';
    echo '<thead>';
    echo '<tr>';
    echo '<th><i class="fas fa-hashtag"></i> ID</th>';
    echo '<th><i class="fas fa-user"></i> Client Name</th>';
    echo '<th><i class="fas fa-phone"></i> Phone Number</th>';
    echo '<th><i class="fas fa-server"></i> Active Services</th>';
    echo '<th><i class="fas fa-cog"></i> Actions</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($clients as $client) {
        $clientName = htmlspecialchars($client->firstname . ' ' . $client->lastname, ENT_QUOTES, 'UTF-8');
        $phoneNumber = htmlspecialchars($client->phonenumber ?: 'N/A', ENT_QUOTES, 'UTF-8');
        $activeServices = (int) $client->active_services;

        // Badge class based on service count
        $badgeClass = 'badge-secondary';
        if ($activeServices > 0) {
            $badgeClass = 'badge-success';
        }
        if ($activeServices >= 5) {
            $badgeClass = 'badge-primary';
        }
        if ($activeServices >= 10) {
            $badgeClass = 'badge-warning';
        }

        echo '<tr>';
        echo '<td><span class="cs-client-id">#' . (int) $client->id . '</span></td>';
        echo '<td><strong>' . $clientName . '</strong></td>';
        echo '<td>' . $phoneNumber . '</td>';
        echo '<td><span class="badge ' . $badgeClass . '">' . $activeServices . '</span></td>';
        echo '<td>';
        echo '<a href="clientssummary.php?userid=' . (int) $client->id . '" class="btn btn-sm btn-info" title="View Client"><i class="fas fa-eye"></i></a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>'; // End table container

    echo '</div>'; // End main container

    // Footer
    echo '<div class="cs-footer">';
    echo '<p><i class="fas fa-code"></i> Developed by <strong>SKYRHRG Technologies System</strong> | Version 1.0</p>';
    echo '</div>';

    // Initialize DataTable
    echo '<script>
    $(document).ready(function() {
        $("#clientsnapshot-table").DataTable({
            "pageLength": ' . $itemsPerPage . ',
            "order": [[3, "desc"]],
            "language": {
                "search": "<i class=\"fas fa-search\"></i> Search:",
                "lengthMenu": "Show _MENU_ clients",
                "info": "Showing _START_ to _END_ of _TOTAL_ clients",
                "paginate": {
                    "first": "<i class=\"fas fa-angle-double-left\"></i>",
                    "last": "<i class=\"fas fa-angle-double-right\"></i>",
                    "next": "<i class=\"fas fa-angle-right\"></i>",
                    "previous": "<i class=\"fas fa-angle-left\"></i>"
                }
            },
            "dom": "<\"cs-table-top\"lf>rt<\"cs-table-bottom\"ip>",
            "responsive": true
        });
    });
    </script>';
}

/**
 * Get Clients with Active Service Counts
 *
 * @param bool $includeInactive Include clients with zero active services
 * @return \Illuminate\Support\Collection
 */
function clientsnapshot_get_clients($includeInactive = true)
{
    $query = Capsule::table('tblclients')
        ->leftJoin('tblhosting', 'tblclients.id', '=', 'tblhosting.userid')
        ->select(
            'tblclients.id',
            'tblclients.firstname',
            'tblclients.lastname',
            'tblclients.phonenumber'
        )
        ->selectRaw('COUNT(CASE WHEN tblhosting.domainstatus = "Active" THEN 1 END) as active_services')
        ->groupBy('tblclients.id', 'tblclients.firstname', 'tblclients.lastname', 'tblclients.phonenumber');

    if (!$includeInactive) {
        $query->havingRaw('active_services > 0');
    }

    return $query->orderBy('active_services', 'desc')->get();
}

/**
 * Export Client Data to CSV or Excel
 *
 * @param string $format Export format (csv or excel)
 * @return void
 */
function clientsnapshot_export($format)
{
    $clients = clientsnapshot_get_clients(true);

    $filename = 'clientsnapshot_export_' . date('Y-m-d_H-i-s');

    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($output, ['Client ID', 'Client Name', 'Phone Number', 'Active Services']);

        // Data rows
        foreach ($clients as $client) {
            fputcsv($output, [
                $client->id,
                $client->firstname . ' ' . $client->lastname,
                $client->phonenumber ?: 'N/A',
                $client->active_services
            ]);
        }

        fclose($output);
        exit;

    } elseif ($format === 'excel') {
        // Generate Excel-compatible XML (SpreadsheetML)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";

        echo '<Styles>';
        echo '<Style ss:ID="Header"><Font ss:Bold="1"/><Interior ss:Color="#4472C4" ss:Pattern="Solid"/><Font ss:Color="#FFFFFF"/></Style>';
        echo '<Style ss:ID="Data"></Style>';
        echo '</Styles>';

        echo '<Worksheet ss:Name="Client Snapshot">' . "\n";
        echo '<Table>' . "\n";

        // Header row
        echo '<Row ss:StyleID="Header">';
        echo '<Cell><Data ss:Type="String">Client ID</Data></Cell>';
        echo '<Cell><Data ss:Type="String">Client Name</Data></Cell>';
        echo '<Cell><Data ss:Type="String">Phone Number</Data></Cell>';
        echo '<Cell><Data ss:Type="String">Active Services</Data></Cell>';
        echo '</Row>' . "\n";

        // Data rows
        foreach ($clients as $client) {
            echo '<Row ss:StyleID="Data">';
            echo '<Cell><Data ss:Type="Number">' . (int) $client->id . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($client->firstname . ' ' . $client->lastname, ENT_QUOTES, 'UTF-8') . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($client->phonenumber ?: 'N/A', ENT_QUOTES, 'UTF-8') . '</Data></Cell>';
            echo '<Cell><Data ss:Type="Number">' . (int) $client->active_services . '</Data></Cell>';
            echo '</Row>' . "\n";
        }

        echo '</Table>' . "\n";
        echo '</Worksheet>' . "\n";
        echo '</Workbook>';

        exit;
    }
}
