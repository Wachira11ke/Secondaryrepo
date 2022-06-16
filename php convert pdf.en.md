+++

title = "Convert a PDF Document to a Preview Image in PHP"
date = 2022-05-08
draft = false
keywords = ["php convert pdf"]
description = "You can convert a PDF document into images with PHP"
tags = ["ImageMagick", "PHP"]
author = "John Wachira"
postlink = 467793
inarticle = true

+++

In this tutorial, we will discuss two methods you can use to convert a PDF document to a set of preview images in PHP. Preview add a layer of security to your content because the content on images can not be copy and pasted. They also offer other functionalities which we will not dwell on.

The easiest way to convert PDF documents to preview images is by utilizing third-party libraries. These are;

1. Ghostscript.
2. ImageMagick.

## Convert PDF Document to Preview Image with PHP and  Ghostscript

This command line utility is available on Windows, Linux, and Mac. Follow these steps to convert PDF documents to preview images;

1. To start the installation, go to Ghostscript's official website and download the executable file and follow the setup instructions.

2. Run the code below to verify the installation;

   ```
   $gs --version
   ```

3. In the directory containing your PDF file, run the command below;

   ```
   $gs -dSAFER -dBATCH -sDEVICE=jpeg \
   -dTextAlphaBits=4 -dGraphicsAlphaBits=4 \ 
   -dFirstPage=1 -dLastPage=1 -r300 \
   -sOutputFile=preview.jpg input.pdf
   ```

4. The command above will create an image of the starting page on your document. We call the `exec()` function to use the command in PHP as shown below;

   ```php
   <?php
   exec( "ls -l", $output_str, $return_val );
   foreach ( $output_str as $line ) {
       echo $line . "\n";
   }
   ?>
   ```

   The code above will load all directories and files to the console. We can now use PHP code to execute the ghostscript command.

5. Here is the PHP script we used.

   ```php
   <?php
    
   function my_pdf ( $file ) {
       $file_info = file_get_contents( $file );
        
       if ( preg_match( "/^%PDF-[0-1]\.[0-9]+/", $file_info ) ) {
           return true;
       }
       else {
           return false;
       }
   }
   function our_preview ( $file ) {
       $our_format = "png";
       $prvw_page = "1";
       $resolution = "300";
       $our_file = "prvw.jpg";
     
       $command  = "gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=" . $our_format . " ";
       $command .= "-dTextAlphaBits=" . " -dGraphicsAlphaBits=" . . " ";
       $command .= "-dFirstPage=" . $prvw_page . " -dLastPage=" . $prvw_page . " ";
       $command .= "-r" . $resolution . " ";
       $command .= "-sOutputFile=" . $our_file . " '" . $file . "'";
       echo "Running command...\n";
       exec( $command, $com_output, $ret_val );
       foreach( $com_output as $line ) {
           echo $line . "\n";
       }
       if ( !$ret_val ) {
           echo "Preview created !!\n";
       }
       else {
           echo "Error while creating.\n";
       }
   }
   function __main__() {
       global $arg;
       $inp_file = $arg[1];
    
       if ( my_pdf( $inp_file ) ) {
           // Preview for the pdf
           create_preview( $inp_file );
       }
       else {
           echo "The  file " . $inp_file . " is not a valid PDF document.\n";
       }
   } 
   __main__();  
   ?>
   ```

   Code execution begins at the `_main_()` function where it fetches the PDF file on the command line and verifies its validity. If the file is valid, PHP will execute the ghostscript command.

   Output;

   ```
   $ php pdf_preview.php input.pdf
   Executing command...
   GPL Ghostscript 9.22 (2022-08-05)
   Copyright (C) 2022 Artifex Software, Inc.  All rights reserved.
   This software comes with NO WARRANTY: see the file PUBLIC for details.
   Processing pages 1 through 1.
   Page 1
   Preview created successfully!!
   ```

   ​

## Convert PDF Document to Preview Image with PHP and ImageMagick

Start by installing all ImageMagick binaries into your system. Run the command below to install ImageMagick dependencies;

```
$sudo dnf install gcc php-devel php-pear
```

Run the command below to install ImageMagick;

```
$ sudo dnf install ImageMagick ImageMagick-devel
```

Let's install the PHP wrapper classes;

```
$ sudo pecl install imagick
$ sudo bash -c "echo "extension=imagick.so" > /etc/php.d/imagick.ini"
```

For those using it on a LAMP server. restart your Apache web server;

```
$ sudo service httpd restart
```

At this point, everything we need is ready. We can now use the PHP script we had earlier and edit the `create_preview()` function. Use the code below;

```php
function create_preview ( $file ) {
    $output_format = "jpeg";
    $preview_page = "1";
    $resolution = "300";
    $output_file = "imagick_preview.jpg";
 
    echo "Fetching preview...\n";
    $img_data = new Imagick();
    $img_data->setResolution( $resolution, $resolution );
    $img_data->readImage( $file . "[" . ($preview_page - 1) . "]" );
    $img_data->setImageFormat( $output_format );
 
    file_put_contents( $output_file, $img_data, FILE_USE_INCLUDE_PATH );
}
```

Output;

```
$ php pdf_preview.php input.pdf
Fetching preview...
```

That is how we create preview images from PDF documents on PHP. Both methods have a similar base functionality, your choice depends on preferences.