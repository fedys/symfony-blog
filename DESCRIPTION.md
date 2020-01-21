# Blog application
Develop a Blog in Symfony framework
## Private Part
* Private part is accessible after successful login using username and password
    * Registration and creating new administrators is not possible, so there will be at
least one predefined account
* Administrator can create/edit blog posts. Each blog post has
    * Title - required short text, up to 150 chars
    * Text - required wysiwyg content
    * Date - required
    * Tags - can have multiple tags
    * Url - unique
* Administrator can disable(hide) blog post. It cannot be deleted
    * Administrator still sees the disabled blog post, but it is not accessible for public
users.
    * Can be re-enabled
* Administrator can see the number of views for each blog post
## Public part
* Shows paginated list of blog posts
    * Ordered by date from the latest to oldest
    * Two records per page
    * Shows title and date
* Every blog post has a detail page with unique URL
    * Adds +1 to blog post views
* REST API with at least two endpoints :
    * Returns the full list of blog posts without textual content and tags
    * Returns the detail of single blog post including textual content and tags
        * Adds +1 to blog post views
# General Requirements
* Symfony framework 3.x, 4.x
* SQL or NOSQL database
* PHP 7.x
* Create a github public repository and send the link
* Include README with the full build process described or any automatic build solution
* Use Composer
