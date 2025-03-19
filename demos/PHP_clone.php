Detailed Explanation

1. PHP_clone.htm (Launch Page):
• Purpose:
Provides an HTML form for users to enter a URL of a webpage.
• How It Works:
• Contains a single text input for the URL and a submit button.
• Uses the GET method to send the user’s input to PHP_clone.php when the form is submitted.

2. PHP_clone.php (Processing Script):

This script is responsible for two main tasks:
• Downloading the HTML source code from the user-provided URL.
• Reformatting (prettifying) that HTML so it is easier to read.

a. Downloading HTML Source with cURL
• Function: getWebPageSource($url)
• What It Does:
• Checks if the cURL extension is available.
• Initializes a cURL session with the provided URL.
• Sets several options:
• Returns the response as a string.
• Follows any redirects.
• Uses a browser-like User-Agent string.
• Bypasses SSL certificate verification.
• Executes the cURL session and retrieves the HTML content.
• If there is an error during retrieval, it outputs the error message.
• Returns the HTML content if successful.

b. Prettifying the HTML Source
• Function: prettifyHTML($html)
• What It Does:
• Uses preg_split with a regular expression to break the HTML into tokens.
• Tokens include HTML tags and the text between them.
• Initializes an indentation counter ($indent) starting at 0.
• Iterates through each token:
• If the token is a closing tag:
• Decreases the indent level.
• Outputs the tag on a new line with the updated indentation.
• If the token is an opening tag:
• Outputs the tag on a new line with the current indentation.
• Increases the indent level if the tag is not self-closing.
• If the token is text content:
• Trims and outputs it on a new line with the current indentation (if not empty).
• Returns the entire reformatted HTML as a string.

c. Main Execution Flow in PHP_clone.php
• Checks if a URL was provided via the GET request.
• Calls getWebPageSource($url) to retrieve the HTML content.
• If the content is successfully retrieved:
• Passes the HTML to prettifyHTML($html) to reformat it.
• Outputs the prettified HTML within
<pre> tags (using htmlspecialchars to preserve formatting in the browser).
•	If no URL is provided or an error occurs, it displays an appropriate message.

Overall Summary

The code consists of two simple files. The first, PHP_clone.htm, provides a form for users to submit a URL. The second, PHP_clone.php,
uses cURL to download the HTML from that URL and then processes the HTML with basic string functions to add proper indentation and newlines.
The final prettified HTML is then displayed in the browser in a readable format. This solution adheres to the professor’s provided code style while
enhancing the readability of the HTML output.

Pseudocode
START

IF URL parameter exists in GET request THEN
    url = GET parameter "url"
    source = getWebPageSource(url)
    
    IF source retrieval successful THEN
        formatted = prettifyHTML(source)
        OUTPUT formatted HTML wrapped in <pre> tags (escape HTML characters)
    ELSE
        OUTPUT "Error retrieving webpage."
    END IF
ELSE
    OUTPUT "No URL provided."
END IF

FUNCTION getWebPageSource(url):
    IF cURL is available THEN
        Initialize cURL session with url
        Set options: return string, follow redirects, set User-Agent, bypass SSL
        Execute cURL session to get html
        IF error in execution THEN
            OUTPUT error message
            RETURN false
        END IF
        Close cURL session
        RETURN html
    END IF
    RETURN false
END FUNCTION

FUNCTION prettifyHTML(html):
    tokens = Split html into tokens (tags and text) using regex
    indent = 0
    output = empty string
    
    FOR each token in tokens DO
        IF token is a closing tag THEN
            Decrease indent by 1
            Append token to output with indent spacing and newline
        ELSE IF token is an opening tag THEN
            Append token to output with indent spacing and newline
            IF token is not self-closing THEN
                Increase indent by 1
            END IF
        ELSE IF token is text THEN
            IF token is not empty THEN
                Append token to output with indent spacing and newline
            END IF
        END IF
    END FOR
    
    RETURN output
END FUNCTION

END
