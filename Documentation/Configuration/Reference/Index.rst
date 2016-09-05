.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)

Reference
^^^^^^^^^

**TSconfig**

- tx\_imagewidthspecificationwizard.

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         hideFieldOnMatch

   Data type
         Boolean

   Description
         If true, the field »imagewidth« will be hidden from the backend as
         soon as the editor selects one of the default values

         The field will reappear if the editor selects the option “Customized
         width” (or whatever label is set in property »ownValueLabel«)

         **Note:**

         This property will be true automatically if »ownValueDisabled« is
         true.

   Default
         1


.. container:: table-row

   Property
         noValueDisabled

   Data type
         Boolean

   Description
         Removes the selectfield option to leave the field »imagewidth« empty.

         **Note:**

         Please keep in mind that this extension does not check the fields
         content, it removes the selectfield option only. So if you want to
         disallow an empty

         field »imagewidth« and force the editor to select a value, then you
         will have to set the property »ownValueDisabled« to true as well.

   Default
         0


.. container:: table-row

   Property
         ownValueDisabled

   Data type
         Boolean

   Description
         If true, the editor won't be able to enter an individual value into
         the field »imagewidth«

   Default
         0


.. container:: table-row

   Property
         noValueLabel

   Data type
         String

   Description
         Description for the option in the selectfield to clear the field
         »imagewidth«

         **Note:**

         With no width given the dimensions of the image will be calculated by
         TYPO3 automatically and the image won't scale as long it is smaller
         than the maximum size set in TypoScript option
         »tt\_content.image.20.maxW« (see css\_styled\_content)

         **Hint:**

         If the string is a reference to locallang label, then the extension
         will try to use that translation. The reference must consists of
         [fileref]:[labelkey], like:

         ::

            LLL:fileadmin/templates/locallang.xml:noValueLabel

   Default
         LLL:EXT:imagewidthspecificationwizard/Resources/Private/Language/
         locallang.xlf:tt\_content.tx\_imagewidthspecificationwizard.noValueLabel


.. container:: table-row

   Property
         ownValueLabel

   Data type
         String

   Description
         Description for the option to choose an individual value

   Default
         LLL:EXT:imagewidthspecificationwizard/Resources/Private/Language/
         locallang.xlf:tt\_content.tx\_imagewidthspecificationwizard.ownValueLabel


.. container:: table-row

   Property
         sizes.[size]

   Data type
         Array

   Description
         Configure all available default sizes of the wizard

         **Syntax:**

         ::

            [size] = [description]

         **Example:**

         ::

            tx_imagewidthspecificationwizard {
              sizes {
                80 = Small image
                160 = Medium image
                420 = Large image
              }
            }

         All descriptions can be a locallang reference, see »noValueLabel«

   Default
         (None)

.. ###### END~OF~TABLE ######


Example
~~~~~~~

Example configuration for three default sizes:

::

   tx_imagewidthspecificationwizard {
     ownValueLabel = Enter an individual value
     sizes {
       75 = 75px - Teaser
       120 = 120px - One third of the content
       360 = 360px - Full content
     }
   }


Setting a default imagesize
~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you want to set a default imagesize, then just use the TSconfig TLO
TCAdefaults. Add this line to your user or page TSconfig to set a
default size of 180 pixel:

::

   TCAdefaults.tt_content.imagewidth = 180

If you have configured the wizard for new content element, then you
might have to overwrite it's settings like this:

::

   mod.wizards.newContentElement.wizardItems.common.elements.textpic.tt_content_defValues.imagewidth = 180
   templavoila.wizards.newContentElement.wizardItems.common.elements.textpic.tt_content_defValues.imagewidth = 180
   mod.wizards.newContentElement.wizardItems.common.elements.image.tt_content_defValues.imagewidth = 180
   templavoila.wizards.newContentElement.wizardItems.common.elements.image.tt_content_defValues.imagewidth = 180

In case you would like to force the editor to select a size out of
your given values only, then you could use this configuration:

::

   tx_imagewidthspecificationwizard {
     noValueDisabled = 1
     ownValueDisabled = 1
     sizes {
       75 = 75px - Teaser
       120 = 120px - One third of the content
       360 = 360px - Full content
     }
   }
   TCAdefaults.tt_content.imagewidth = 120
