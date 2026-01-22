# ClientSnapshot Pro

<p align="center">
  <img src="logo.png" alt="ClientSnapshot Pro Logo" width="100">
</p>

<p align="center">
  <strong>A Professional WHMCS Addon Module for Client Data Overview & Export</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#screenshots">Screenshots</a> •
  <a href="#requirements">Requirements</a> •
  <a href="#license">License</a>
</p>

---

## Overview

**ClientSnapshot Pro** is a free, open-source WHMCS addon module that provides a clean, professional dashboard to view client data including names, phone numbers, and active service counts. Export your data to CSV or Excel with a single click.

Built following **WHMCS Marketplace Guidelines** with security best practices.

## Features

✅ **Clean Dashboard** - Modern, responsive UI with statistics cards  
✅ **Client Data Table** - View all clients with sortable DataTable  
✅ **Active Services Count** - See how many active services each client has  
✅ **CSV Export** - Download client data as CSV file  
✅ **Excel Export** - Download client data as Excel file  
✅ **Admin Widget** - Quick stats on WHMCS admin dashboard  
✅ **Secure** - Uses Capsule ORM (no raw SQL), XSS protected  
✅ **WHMCS 8.x+ Compatible** - Built for modern WHMCS versions  

## Requirements

- WHMCS 8.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher

## Installation

### Method 1: Download ZIP

1. Download the latest release from [Releases](https://github.com/skyrhrg/clientsnapshot-pro/releases)
2. Extract the `clientsnapshot` folder
3. Upload to `your-whmcs/modules/addons/`
4. Go to **Setup → Addon Modules** in WHMCS Admin
5. Find "ClientSnapshot Pro" and click **Activate**
6. Configure access permissions
7. Access via **Addons → ClientSnapshot Pro**

### Method 2: Git Clone

```bash
cd /path/to/whmcs/modules/addons/
git clone https://github.com/skyrhrg/clientsnapshot-pro.git clientsnapshot
```

Then activate in WHMCS Admin as described above.

## Configuration

| Setting | Description | Default |
|---------|-------------|---------|
| Items Per Page | Number of clients shown per page | 25 |
| Show Inactive Clients | Include clients with zero active services | Yes |

## Screenshots

### Dashboard View
The main dashboard displays client statistics and a searchable data table.

### Export Options
- **Export CSV** - Downloads `clientsnapshot_export_YYYY-MM-DD_HH-MM-SS.csv`
- **Export Excel** - Downloads `clientsnapshot_export_YYYY-MM-DD_HH-MM-SS.xls`

## File Structure

```
clientsnapshot/
├── clientsnapshot.php      # Main module file
├── whmcs.json              # WHMCS module manifest
├── hooks.php               # Admin dashboard widget
├── index.php               # Security redirect
├── logo.png                # Module icon
├── README.md               # This file
├── LICENSE                 # GPL-3.0 License
├── CHANGELOG.md            # Version history
└── assets/
    ├── css/
    │   └── style.css       # Custom styling
    └── index.php           # Security redirect
```

## Technical Details

- **Database**: Uses WHMCS Capsule ORM (Laravel Query Builder)
- **Tables Queried**: `tblclients`, `tblhosting`
- **Security**: All inputs escaped, WHMCS access check on all files
- **No Custom Tables**: Module does not create any database tables

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Support

- **Issues**: [GitHub Issues](https://github.com/skyrhrg/clientsnapshot-pro/issues)
- **Website**: [SKYRHRG Technologies System](https://skyrhrgts.com)

## License

This project is licensed under the **GNU General Public License v3.0** - see the [LICENSE](LICENSE) file for details.

## Author

**SKYRHRG Technologies System**  
Website: [skyrhrgts.com](https://skyrhrgts.com)

---

<p align="center">
  Made with ❤️ for the WHMCS Community
</p>
