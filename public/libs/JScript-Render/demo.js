$jS.ready(function(){

   /* Validators */
   var Validator = $jS.validator;

   var Alnum = new Validator.Alnum();
   var StringLength = new Validator.StringLength({ min: 3, max: 8 });
   var _Date = new Validator.Date();
   var Digits = new Validator.Digits();

   var Html = $jS.html;

   function logValidator(validator, input, event, successMessage, boxMessage) 
   {
      document.querySelector(input).addEventListener(event, function(){
         if (validator.isValid(document.querySelector(input).value))
            document.querySelector(boxMessage).innerText = successMessage;
         else
            document.querySelector(boxMessage).innerText = JSON.stringify(validator.getMessages());
      });
   }

   logValidator(Alnum, "#alnum", 'keyup', "Valid input ... OK", "#alnum_validator_response");
   logValidator(StringLength, "#stringLength", 'keyup', "Valid input ... OK", "#stringLength_validator_response");
   logValidator(_Date, "#date", 'keyup', "Valid input ... OK", "#date_validator_response");
   logValidator(Digits, "#digits", 'keyup', "Valid input ... OK", "#digits_validator_response");

   var MathExpression = new Validator.MathExpression('unaryRegEx');

   logValidator(MathExpression, "#mathexpression_1", 'keyup', "Valid expression ... OK", "#mathexpression_validator_response_1");

   MathExpression.setMode('multipleRegEx')
   logValidator(MathExpression, "#mathexpression_2", 'keyup', "Valid expression ... OK", "#mathexpression_validator_response_2");

   MathExpression.setMode('simpleAssociativeRegEx');
   logValidator(MathExpression, "#mathexpression_3", 'keyup', "Valid expression ... OK", "#mathexpression_validator_response_3");

   document.querySelector("#overlay-demo").addEventListener("click", function(){
      var overlay = new $jS.html.Overlay();
      overlay.show();
      setTimeout(function(){ overlay.hide(); },1000);
   });

   /* Body loader */

   document.querySelector("#body-loader").addEventListener("click", function(){
      var loader = new $jS.html.Loader();
      loader.show();
      setTimeout(function(){ loader.hide(); },1000);
   });

   /* Context loaders */

   var ctx1 = document.querySelector("#context-loader-1");

   var context1 = ctx1.getAttribute("data-context");
   var loader1 = new $jS.html.Loader({ context: document.querySelector("#" + context1) });

   ctx1.addEventListener("click", function(){
   	if (loader1.isActive())
   		loader1.hide();
   	else
   		loader1.show();
   });

   var ctx2 = document.querySelector("#context-loader-2");

   var context2 = ctx2.getAttribute("data-context");
   var loader2 = new $jS.html.Loader({ context: document.querySelector("#" + context2), width: 150, height: 40 });

   ctx2.addEventListener("click", function(){
   	if (loader2.isActive())
   		loader2.hide();
   	else
   		loader2.show();
   });

   /* Dialog */

   document.querySelector("#dialog-example").addEventListener('click', function(){
      var dialog = new Html.Dialog({
         id: "dialog-example-action",
         title: "Dialog example",
         width: "300px",
         content: "<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>"
      });
      dialog.show();
   });

   /* Form */

   var form = new $jS.html.Form();

   form.add({
      name: "username",
      type: "text",
      options: {
         label: "Username",
      },
      attributes: {
         placeholder: "user",
         class: 'jInput'
      }
   }).add({
      name: "password",
      type: "password",
      options: {
         label: "password",
      },
      attributes: {
         placeholder: "pass",
         class: 'jInput',
      }
   }).add({
      name: "submit",
      type: "submit",
      attributes: {
         value: "Login",
         class: 'btn btn-default'
      }
   });

   form.setData({ username: 'George', password: '123456' });

   var myForm = form.getForm();

   document.querySelector("#createForm").addEventListener('click', function(){
      document.querySelector("#formArea").appendChild(myForm);
   });

   /* File reader */

   var Reader = $jS.reader;

   var _files = new Reader.File({
      fileBox: document.querySelector("#file-reader-onchange"),      // input[type='file']
      dropBox: document.querySelector("#file-reader-ondrop"),        // dropbox
      preview: document.querySelector("#file-reader-ondrop")         // preview
   });

   _files.addDropEvent();
   _files.addChangeEvent();


   var dateControl = new $jS.utils.DateControl("#dateControlYear", "#dateControlMonth", true);

   document.querySelector("#dateControlYear").addEventListener('change', function(){
      dateControl.render();
   });


   var form = new JScriptRender.html.FormValidator("#processForm", {
      validators: {
         'number_form_process': {
            "StringLength": { "min": 2, "max": 5 },
            "Digits": {},
         },
         'alnum_form_process': {
            "StringLength": { "min": 4, "max": 8 },
            "Alnum": { 'allowWhiteSpace' : false },
         }
      },
      onValid: function(elements)
      {
         alert('success');
      },
      onInvalid: function(messages)
      {
         console.info(messages)
         alert('invalid');
      },
      highlight: { onValid: 'input-success', onInvalid: 'input-error'},
      showMessages: true
   });

   document.querySelector("#processForm").addEventListener("submit", function(event){
      event.preventDefault();
      form.render();      
   });


});