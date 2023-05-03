# 2.0.0
- [Issue-43] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/43) Shopware 6.5 compatibility
- [Issue-33] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/33) Add a new toggle in the Comparison page to show only properties have differences between products
- [Issue-41] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/41) Support for custom fields (except type json, media, entity), the product's custom fields will be shown in the comparison table if they're pre-configured in Plugin's configuration
- Fix various UI bugs and compatible with 6.5 styles: adjust classes name, blocks, styling
- Nicer print output - Remove redundant lines, spaces, and cookie bar on printing
- Overview and Specification sections are now collapsible

# 1.2.2
- Leerzeichen zwischen Tags hinzufügen

# 1.2.1
- Use plugin name as namespace for own templates: there are errors thrown on 6.4.5.0 for not findable template
- Never show rating if review is disabled in shop
- Collect all possible propertyGroups of each product for comparison: we are having some different properties. The customer should always see all properties related to compared products

# 1.2.0
- [Issue-20] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/20) Fixes cross-sellings compare does not work with product variants
- [Issue-19] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/19) Added a new plugin's config to hide attributes (description/price/rating/manufacturer or the whole section)

# 1.1.0
- Shopware 6.4 compatibility
- [Issue-14] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/14) Fixes cross-sellings compare does not work with product variants
- Show variant's property option value in compare list
- [Issue-15] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/15) Added a new plugin's config to toggle between show all/selected properties
- Fix minor issues

# 1.0.6
- [Issue-11] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/11) Fix import scss syntax.
- Small refactor on plugin's base.html.twig

# 1.0.5
- Small fix on handle click events.
- [Issue-9](https://github.com/FriendsOfShopware/FroshProductCompare/issues/9) Add Plugin config `showIconOnly` that only show AddToCompare button with only icon (without Text).

# 1.0.4
- Use `HttpClient` instead of `StoreApiClient`.

# 1.0.3
- Fix Cross Selling is not working after installing the plugin.

# 1.0.2

- Shopware 6.3 compatibility

# 1.0.1 - Release on Shopware store as Frosh's plugin

- Remove some redundant LoC.
- Add robot noindex meta on Compare page
- Update the plugin's name and add some store's requirements.

# 1.0.0 - First release
- Add `Add to Compare` button in Product Card and Product detail.
- Add `Float button` on bottom left of the page with `added products counter`.
- Add `Products added to compare list offcanvas` when click `Float button`.
- Add `Compare page` to display Compare products table. Up to 4 products can be added into the list.
- Add `Print` button in Compare page to print the compare table.
- Add `Clear all` button to clear all products in the list.
- Add `Comparable switch` in `Administration > Product Detail > Cross-selling tab` that allow to display cross-selling products as a compare table.

