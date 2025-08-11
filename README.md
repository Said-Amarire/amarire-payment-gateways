# Amarire Payment Gateways (Dynamic Total-Based Fees)

[![License: GPL v2](https://img.shields.io/badge/license-GPLv2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

## Overview  
Amarire Payment Gateways is a flexible WooCommerce plugin designed to add multiple **custom payment gateways** representing your bank accounts or payment methods. Each gateway supports dynamic fees calculated as a percentage or fixed amount based on the cart total. This plugin helps merchants manage manual or offline payment methods efficiently with clear fees, logos, and instructions.

---

## Features

- Register up to 30 custom payment gateways dynamically.  
- Set fixed fees, percentage fees, or both per gateway.  
- Upload logos to display during checkout for each payment method.  
- Display detailed payment instructions below each payment method.  
- Dynamically update payment fees and order totals on checkout when customers switch payment methods.  
- Store and display payment fee details in order metadata, emails, thank you page, admin order details, and customer account orders.  
- Disable default WooCommerce payment gateway pre-selection for better UX.  
- Suitable for manual payment confirmation workflows or external payment verification links.

---

## Installation

1. Upload the `amarire-payment-gateways` plugin folder to the `/wp-content/plugins/` directory on your WordPress server.  
2. Activate the plugin through the WordPress Plugins menu.  
3. Go to **WooCommerce → Settings → Payments** to enable and configure each payment gateway.  
4. Set titles, fees, logos, and payment instructions as needed.  
5. Test the checkout process to confirm fees and totals update dynamically.

---

## Usage

- At checkout, customers select from your configured payment gateways.  
- Payment fees appear dynamically based on the chosen gateway and cart total.  
- After order placement, payment method and fees are saved and displayed in customer accounts, emails, and admin screens.  
- Use the detailed instructions to guide customers on how to pay manually or confirm payment externally.

---

## File Structure

amarire-payment-gateways/
- amarire-payment-gateways.php  # Main plugin bootstrap file
- includes/  # Plugin core PHP classes
  - class-amarire-abstract-gateway.php
  - class-amarire-gateway-bank.php
  - fees-handlers.php
  - ajax-handlers.php
- assets/  # Plugin assets (JS, images)
  - js/
    - payment-fee-update.js



---

## Technical Details

- Uses WooCommerce’s native payment gateway API to create custom gateways.  
- Hooks into cart fee calculations to add dynamic fees.  
- Utilizes AJAX for live fee and total recalculations on checkout page without reload.  
- Saves payment fee info as order meta data for use in emails and admin screens.  
- Prevents default payment method auto-selection for improved user experience.

---

## License

This project is licensed under the [GNU General Public License v2 (GPL-2.0)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html).

---

## Contribution

Feel free to fork this repository, make enhancements, or report issues. Pull requests are welcome!

---

## Credits

Developed by Said Amarire (Amarire Dev) with the assistance of AI tools.

---

## Contact

- GitHub: [https://github.com/Said-Amarire](https://github.com/Said-Amarire)  
- Website: [https://amarire.dev](https://amarire.dev)  

---

Thank you for using Amarire Payment Gateways!
