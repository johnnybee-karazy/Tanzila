From Tanzila of abelohost
    
- Create a custom post type called “Cities.”
- On the post editing page, create a meta box with custom fields “latitude” and “longitude” for entering the latitude and longitude of the city, respectively. Create additional fields if necessary.
- Create a custom taxonomy titled “Countries” and attach it to “Cities.”
- Create a widget where a city from the custom post type “Cities.” The widget should display the city name and the current temperature using an external API (e.g., OpenWeatherMap).
- On a separate page with a custom template, display a table listing countries, cities, and temperatures. Retrieve the data for the table using a database query with the global variable $wpdb. Add a search field for cities above the table using WP Ajax. Add custom action hooks before and after the table.
