# Komoju Payment Plugin for EC-CUBE 4.3

This plugin integrates Komoju Payment Gateway with EC-CUBE 4.3.

## Features

- Supports Credit Card payments via Komoju.
- Seamless integration with EC-CUBE 4.3 checkout flow.
- Configurable settings via EC-CUBE Admin.

## Installation

1. Upload the plugin directory `KomojuPayment43` to `app/Plugin/` of your EC-CUBE installation.
2. Run the installation command:
   ```bash
   bin/console eccube:plugin:install --code=KomojuPayment43
   bin/console eccube:plugin:enable --code=KomojuPayment43
   ```
3. Clear cache:
   ```bash
   bin/console cache:clear
   ```

## Configuration

1. Go to **Owner's Store > Plugins > Plugin List**.
2. Click **Settings** (設定) for "Komoju Payment Plugin".
3. Enter your Komoju Secret Key and Publishable Key.
4. Configure Webhook URL in your Komoju Dashboard to point to your site's webhook endpoint.

## License

[MIT License](LICENSE)
