From Tanzila of abelohost

Tasks
- Create a custom post type called “Cities.”
- On the post editing page, create a meta box with custom fields “latitude” and “longitude” for entering the latitude and longitude of the city, respectively. Create additional fields if necessary.
- Create a custom taxonomy titled “Countries” and attach it to “Cities.”
- Create a widget where a city from the custom post type “Cities.” The widget should display the city name and the current temperature using an external API (e.g., OpenWeatherMap).
- On a separate page with a custom template, display a table listing countries, cities, and temperatures. Retrieve the data for the table using a database query with the global variable $wpdb. Add a search field for cities above the table using WP Ajax. Add custom action hooks before and after the table.

Requirements
- Use the Storefront theme (https://wordpress.org/themes/storefront/). Make all modifications in a child theme.
- Do not use plugins to complete the task.
- All code must be documented.
- Write optimized and performant code.
- The project file structure should be organized and logical.


How to use codes and files
- after wordpress install use storefront theme
- copy functions.php to theme folder (wp-content/theme/storefront/)
- copy the template file (countrylisting.php) to the same folder (wp-content/theme/storefront/)
- create a page from the backend and use or change the template to CountryListing 
