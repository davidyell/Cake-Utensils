# Cake Utensils
This is a utility plugin hopefully full of useful bits and bobs you can use to build your CakePHP applications.

## What does it include?
So what utensils are included to help my cake baking?

### Encrypted string data type
A data type class for working with encrypted strings. This class allow you to inline encrypt fields into your database 
and read them out again.

### Postcode data type
This data type class uses the [vasildakov/postcode](https://github.com/vasildakov/postcode) library to marshall UK 
postal codes into value objects to help with validation, formatting and display.

### Telephone data type
A data type class which uses [giggsey/libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) library 
to marshall telephone numbers into value objects to help with validation, formatting and easier methods for getting 
specific parts of phone numbers such as dialing codes.

[How do I use custom data types?](https://book.cakephp.org/3.0/en/orm/database-basics.html#adding-custom-types)

### Security middleware
A basic middleware for adding some extra headers to requests based on the [OWASP Secure headers](https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#tab=Headers)

[How do I add middleware?](https://book.cakephp.org/3.0/en/controllers/middleware.html#adding-middleware-from-plugins)

## License
Please see [LICENSE.md](LICENSE.md)
