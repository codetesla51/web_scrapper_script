<?php
// Function to fetch proxies from the proxies.txt file
function get_proxies()
{
  $file = "proxies.txt";
  if (file_exists($file)) {
    return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  } else {
    echo "Proxy file not found!\n";
    return [];
  }
}

// Function to fetch user agents from the user-agents.txt file
function get_user_agents()
{
  $file = "user_agents.txt";
  if (file_exists($file)) {
    return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  } else {
    echo "User-agent file not found!\n";
    return [];
  }
}

// Set the target URL for scraping
$start_url = "your-url.com"; // Change this to the URL you want to scrape

$visited_urls = []; // Track URLs already visited
$max_depth = 3; // Maximum depth for recursive scraping (to avoid going too deep)

/**
 * Main function to start scraping a website.
 */
function scrape_site($url, $depth = 0)
{
  global $visited_urls, $max_depth;

  // If we've exceeded the maximum depth or already visited this URL, stop
  if ($depth > $max_depth || in_array($url, $visited_urls)) {
    return;
  }

  echo "Scraping: $url\n";

  // Fetch the HTML content of the URL
  $html = fetch_html($url);
  if (!$html) {
    return; // Stop if we couldn't retrieve HTML content
  }

  // Save the HTML to a local file
  save_html_to_file($url, $html);

  // Mark this URL as visited
  $visited_urls[] = $url;

  // Extract internal links and recursively scrape them
  $links = extract_internal_links($html, $url);
  foreach ($links as $link) {
    scrape_site($link, $depth + 1);
  }
}

/**
 * Fetch the HTML content from the given URL using cURL.
 */
function fetch_html($url)
{
  $proxies = get_proxies();
  $user_agents = get_user_agents();

  if (empty($proxies) || empty($user_agents)) {
    echo "No proxies or user-agents available for scraping.\n";
    return false;
  }

  // Pick a random proxy and user-agent to make the request look more authentic
  $random_proxy = $proxies[array_rand($proxies)];
  $random_user_agent = $user_agents[array_rand($user_agents)];

  // Set up the cURL request
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_PROXY, $random_proxy); // Use the random proxy
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: " . $random_user_agent]); // Set the random user agent
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

  $html = curl_exec($ch);

  // Handle any cURL errors
  if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
    curl_close($ch);
    return false;
  }

  curl_close($ch);
  return $html;
}

/**
 * Save the HTML content to a local file.
 */
function save_html_to_file($url, $html)
{
  // Create a directory to save the files if it doesn't exist
  $folder = "scraped_pages";
  if (!is_dir($folder)) {
    mkdir($folder);
  }

  // Generate a filename based on the URL
  $parsed_url = parse_url($url);
  $path = isset($parsed_url["path"]) ? $parsed_url["path"] : "index";
  $filename = $folder . "/" . trim(str_replace("/", "_", $path), "_") . ".html";

  // Save the HTML content to the file
  file_put_contents($filename, $html);
  echo "Saved HTML to: $filename\n";
}

/**
 * Extract internal links from the HTML content.
 */
function extract_internal_links($html, $base_url)
{
  $dom = new DOMDocument();
  @$dom->loadHTML($html); // Suppress warnings for malformed HTML

  $links = [];

  // Extract all <a> tags and their href attributes
  foreach ($dom->getElementsByTagName("a") as $node) {
    $href = $node->getAttribute("href");
    if (!empty($href)) {
      $absolute_url = make_absolute_url($href, $base_url);

      // Only include links that belong to the original domain
      $parsed_base = parse_url($base_url);
      $parsed_link = parse_url($absolute_url);

      if (
        isset($parsed_link["host"]) &&
        $parsed_link["host"] == $parsed_base["host"]
      ) {
        $links[] = $absolute_url;
      }
    }
  }

  // Remove duplicates and return unique internal links
  return array_unique($links);
}

/**
 * Convert a relative URL to an absolute URL based on the base URL.
 */
function make_absolute_url($relative_url, $base_url)
{
  if (parse_url($relative_url, PHP_URL_SCHEME) != "") {
    return $relative_url;
  }

  // Build the absolute URL by appending the relative URL to the base URL
  return rtrim($base_url, "/") . "/" . ltrim($relative_url, "/");
}

// Start the scraping process from the initial URL
scrape_site($start_url);
