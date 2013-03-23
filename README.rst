--------------------------
GlorpenStyleEmbedderBundle
--------------------------

Reads CSS styles and applies it to html elements.

Since css is embedded into style attribute, pseudo selectors are not supported (:hover,:nth-child, etc).

You can use any css selector combination. Multiple selectors will be applied to single element with accounting for css selector specifity, so you can write:

.. sourcecode: css

   * { color: red; }
   #myId { color: blue; }


How to install
==============

- add requirements to composer.json:

.. sourcecode:: json

   {
       "require": {
           "glorpen/style-embedder-bundle": "@dev"
       }
   }
   

- enable the plugin in your **AppKernel** class

*app/AppKernel.php*

.. sourcecode:: php

    <?php
    
    class AppKernel extends AppKernel
    {
       public function registerBundles()
       {
           $bundles = array(
               ...
               new Glorpen\StyleEmbedderBundle\GlorpenStyleEmbedderBundle(),
               ...
           );
       }
    }


Usage
=====

Rendering with Twig
*******************

Template:

.. sourcecode:: twig

   {% block style %}
      .footer * {
         color: silver;
      }
     .footer p {
         font-weight: bold;
      }
      .footer p > span {
         font-weight: normal;
      }
      h1 {
         font-size: 20px;
      }
   {% endblock %}
   {% block html %}
   <html>
      <head></head>
      <body>
         <h1>Some Header</h1>
         <div class="footer">
            <p>Address: <span>Our address</span></p>
            <p>Tel.: <span>123-456-789</span></p>
         </div>
      </body>
   </html>
   {% endblock %}
   

Render:

.. sourcecode:: php

   <?php
   
   $embedder = $container->get("glorpen.style_embedder")
   $ret = $embedder->render('template.html.twig');
   

You will get:

.. sourcecode:: html

   <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
   <html><head></head><body>
      <h1 style="font-size:20px;">Some Header</h1>
      <div class="footer">
         <p style="color:silver;font-weight:bold;">Address: <span style="color:silver;font-weight:normal;">Our address</span></p>
         <p style="color:silver;font-weight:bold;">Tel.: <span style="color:silver;font-weight:normal;">123-456-789</span></p>
      </div>
   </body></html>


Simple rendering
****************

Embedder can handle plain data too.

.. sourcecode:: php

   <?php
   
   $styles = '* { font-weight: bold; }';
   $html = ' .... ';
   
   $embedder = $container->get("glorpen.style_embedder")
   $ret = $embedder->embed($styles, $html);
   
