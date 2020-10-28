<?php
//Import the global functions
include_once dirname($_SERVER["DOCUMENT_ROOT"])."/core/global-functions.php";
//Import config file
include_once include_local_file("/includes/a_config.php");
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
  <!-- Head tags -->
  <? include_once include_local_file("/includes/head-tags.php");?>
  <!--Custom CSS-->
  <style>
  @import url('https://fonts.googleapis.com/css2?family=Cousine&display=swap');
  @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@500&display=swap');
  </style> 
</head>
<body>
  <!-- Navbar -->
  <? include_once include_local_file("/includes/navbar.php");?>
  <!-- Content -->
  <div id="wrapper"  style="background-color: #D9DFE3">
    <div class="container section">

      <!--Title and subtitle-->
      <div class="has-text-centered mb-5">
        <h1 class="title is-1">Generate a strong password</h1>
        <h3 class="subtitle">Use one of our randomly generated passwords</h3>
      </div>

      <!--Password View Section-->
      <div class="columns is-vcentered is-centered is-mobile mt-5">
        <!--Generated password label -->
        <div id="securityIndicator" class="column is-9 is-centered has-background-secure border3">
          <div class="columns">
            <div class="column is-10">
              <h4 style="white-space: nowrap;overflow: hidden; overflow-x: scroll; height: 100%; font-family: 'Cousine', monospace;" id="passwordView" class="is-size-4 has-text-light">aasjhd23413lsd</h4>
            </div>
            <div class="column is-2">
              <!--Level for buttons -->
              <div class="level is-mobile">
                <div class="level-item">
                  <button data-tooltip="Copy to Clipboard" onclick="copyToClipboard()" class="button has-tooltip-right"><span class="icon"><i class="far fa-copy"></i></span></button>
                </div>
                <div class="level-item">
                  <button data-tooltip="Regenerate password" id="refresh" class="button has-tooltip-right"><span class="icon"><i class="fas fa-sync-alt"></i></span></button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!--Password Config Section-->
      <div class="columns is-centered is-mobile mt-5">
        <!--Customise Section -->
        <div style="font-family: 'Roboto Mono', monospace;" class="column is-9 is-centered has-background-light border3">
          <!--Strength section-->
          <h4 id="strengthLabel" class="is-size-6 mb-1">Strength: 25</h4>
          <h4 id="crackTimeLabel" class="is-size-6 mb-1">Time to crack: ???</h4>
          <hr style="background-color: #E3E2E4">
          <!--Length label-->
          <h4 id="lengthLabel" class="is-size-5 mb-1">Length: 25</h4>
          <!--Length Slider-->
          <div class="slidecontainer">
            <input id="lengthSlider" type="range" min="3" max="35" value="10" class="slider" style="width: 100%">
          </div>
          <br class="mt-4">
          <!--Customise controls-->
          <fieldset id="passwordParameters">
            <div class="level">
              
                <!--Numbers-->
                <div class="level-item">
                  <label class="checkContainer has-text-centered">Numbers
                    <input id="numCheck" type="checkbox" checked="checked">
                    <span class="checkmark"></span>
                  </label>
                </div>
                <!--Letters-->
                <div class="level-item">
                  <label class="checkContainer has-text-centered">Letters
                    <input id="letCheck" type="checkbox" checked="checked">
                    <span class="checkmark"></span>
                  </label>
                </div>
                <!--Symbols-->
                <div class="level-item">
                  <label class="checkContainer has-text-centered">Symbols
                    <input id="symCheck" type="checkbox" checked="checked">
                    <span class="checkmark"></span>
                  </label>
                </div>     
            </div>
          </fieldset>  
        </div>
      </div>

    </div>
  </div>
  <!-- Footer -->
  <? include_once include_local_file("/includes/footer.php");?>
  <!--Scripts-->
  <script type="text/javascript">
    
    /* =================== F U N C T I O N S ====================

    /*
     * Will generate the complex password
     */
    function generate(length){
      var password="";
      //Setup an array for all letters and numbers etc
      var all = ""
      //Add the correct letters, numbers etc depending on check state
      all+=(addSet("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz","letCheck"));
      all+=(addSet("0123456789","numCheck"));
      all+=(addSet(";!£$&'#,?{}[]()+=*<>~","symCheck"));
      //Create the password
      for ( var i = 0; i < length; i++ ) {
        password += all.charAt(Math.floor(Math.random() * all.length));
      }
      //Estimate time
      $("#crackTimeLabel").text("Time to crack: "+convertTime(estimateTime(all.length,length)));
      return password;
    }

    /*
     * Function will add a data set to the all list given its widget ID
     */
    function addSet(chars,widgetID){
      //Only add the set if the checkbox is checked
      if($("#"+widgetID).is(':checked')){
        return chars
      }
      return "";
    }

    /*
     * Will generate the complex password
     * as well as updating the correct labels
     */
    function update(length){
      //Update the password label with a generated password
      password=generate(length);
      $("#passwordView").text(password);
      //Update the length label
      let lengthContent="Length: "+length;
      $("#lengthLabel").text(lengthContent);
      //Update colours
      updateColours(rankPassword(password))
    }

    /*
     * Function will get the current value
     * of the slider and generate a password
     */

    function getSliderAndUpdate(){
      var val = document.getElementById("lengthSlider").value
      update(val);
    }

    /* Function will copy password to clipboard*/

    function copyToClipboard(){
      //Get the password label element
      element=("#passwordView")
      //Copy to clipboard using temp input field
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val($(element).text()).select();
      document.execCommand("copy");
      $temp.remove();

      //Alert
      alert("Copied password");
    }


    /*
     * Function will rank a generated password
     * returns 1, 2 or 3 (3 being best)
     */

    function rankPassword(password){
      len=password.length
      if (len < 6){
        return 1
      }else if(len < 10){
        return 2
      }
      return 3
    }

    /*
     * Will return in seconds the amount of time
     * to crack password
     */
    function estimateTime(setLength,passwordLength){
      //Super computers
      return 0.5*Math.pow(setLength,passwordLength) * 1.21e-11;
      //Standard computers
      //return 0.5*Math.pow(setLength,passwordLength) * 1.764e-8;
    }

    /*
     * Will convert time in seconds to a more 
     * readable format
     */
    function convertTime(time){
      //Array to convert time to a string value
      var data=[["Seconds",60],["Minutes",3600],["Hours",86400],["Days",3.154e7],["Years",3.154e8],["Decades",3.154e9],["Centurys",3.154e10],["Milleniums",3.154e11]];
      //Setup response
      response="???"
      if (time < 1){
        response="Less than a second"
      }else{
        //Loop through time stamps
        for (var i =0; i < data.length;i++){
          item=data[i]
          //If the generated time is less than the current timestamp
          if (time < item[1]){
            var divisor = 1
            if (i > 0){
              divisor=data[i-1][1];
            }
            //Divide the generate time by the divisor and create a string
            response = Math.round(time/divisor)+" "+item[0];
            break;
          }
          response="Over 1 Million Years"
            
        }
      }
      return response;
    }


    /*
     * Will update the ui based on the generated passwords
     * rank
     */

    function updateColours(passwordRank){
      //Remove all classes
      allClasses=["secure","medium","insecure"]
      for (i = 0; i < allClasses.length; i++) {
        $("#securityIndicator").removeClass("has-background-"+allClasses[i]);
        $("#strengthLabel").removeClass("has-text-"+allClasses[i]);
      } 
      switch(passwordRank) {
        case 1:
          $("#securityIndicator").addClass("has-background-insecure");
          //Update label and colour
          $("#strengthLabel").text("Strength: Insecure");
          $("#strengthLabel").addClass("has-text-insecure");
          break;
        case 2:
          $("#securityIndicator").addClass("has-background-medium");
          //Update label and colour
          $("#strengthLabel").text("Strength: Medium");
          $("#strengthLabel").addClass("has-text-medium");
          break;
        default:
          $("#securityIndicator").addClass("has-background-secure");
          //Update label and colour
          $("#strengthLabel").text("Strength: Secure");
          $("#strengthLabel").addClass("has-text-secure");
      } 
    }

    /* =================== B I N D I N G S ====================

    /*
     * Binding when a checkbox is clicked
     */
    $('#passwordParameters .checkContainer input').change(function() {
      //If it's currently disabled then simply enable it
      if (this.checked){
        $(this).prop("checked", true);
        //Regenerate when enabled again
        getSliderAndUpdate();
      }
      //Check at least 2 checkboxes are selected
      else if ($('#passwordParameters .checkContainer input:checked').length >= 1){
        //Disable the checkbox
        $(this).prop("checked", false);
        //Regenerate when disabled
        getSliderAndUpdate();

      }else{
        //Force this checkbox to be enabled
        $(this).prop("checked", true);
      }  
    });


    //Whenever slider is moved
    $("#lengthSlider").on("input change", function() {
      //Get the slider value
      var value = this.value;
      //Update
      update(value);
    });



    $( "#refresh" ).click(function() {
      getSliderAndUpdate();
    });


    //JAVASCRIPT FIRST CALLS
    $( document ).ready(function() {
       getSliderAndUpdate(); 
    });


  </script>
</body>
</html>