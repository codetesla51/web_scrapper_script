# Web Scra with Proxy and User-Agent Rotation

This PHP script provides a simple web scraping solution with added features such as proxy rotation and user-agent rotation. It allows you to scrape multiple web pages while avoiding detection through IP blocking or user-agent filtering. The scraped HTML content is saved locally, and the scraper also has a recursive feature for internal links.

## Features

- **Proxy Rotation**: The script uses a list of proxies, rotating them randomly to avoid getting blocked by websites.
- **User-Agent Rotation**: Random user-agent strings are selected to mimic requests from different browsers and devices.
- **Internal Link Scraping**: The scraper can recursively follow and scrape internal links from the base URL.
- **Customizable Depth**: You can control how deep the recursion goes when
following internal links more depths slower scrapping .
- **HTML Saving**: All scraped HTML pages are saved locally in a structured format for later use.

## Requirements

- PHP 7.4+ with `cURL` enabled.
- Basic knowledge of web scraping and working with PHP.

## Installation

1. Clone the repository:
    ```bash
    https://github.com/codetesla51/web_scrapper_script.git
    cd web_scrapper_script
    ```

2. Ensure `cURL` is enabled in your PHP installation:
    ```bash
    sudo apt install php-curl
    ```
- termux
    ```bash
     apt install php-curl
    ```
3. Create a folder named `scraped_pages` in the root directory to store the scraped HTML files:
    ```bash
    mkdir scraped_pages
    ```

4. Create your proxy list file:
    - Add a `proxies.txt` file in the root directory, with a list of proxies in the format `ip:port`, one per line.
  
    Example:
    ```
    192.168.1.1:8080
    192.168.1.2:8080
    ```

5. Add user-agents to a file:
    - Create a file named `user_agents.txt` in the root directory. Add different user-agent strings, one per line.

    Example:
    ```
    Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3
    Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Safari/605.1.15
    ```

## Usage

1. Run the scraper by executing the PHP script:
    ```bash
    php scraper.php
    ```

2. The script will scrape the specified base URL, follow internal links up to the defined depth, and save the HTML content of each page in the `pages` directory.

3. Logs will be printed in the console to show the scraping progress.

## Configuration

- To change the base URL or the maximum depth higher depths scrapping becomes
slower .of scraping, modify the script:
    ```php
    $base_url = 'https://example.com';
    $max_depth = 1;  // Default is 1 levels deep 
    ```

- To add new proxies or user-agents, update the `proxies.txt` or `user-agents.txt` files with new entries.
- star if you find useful ,creat pull requests.