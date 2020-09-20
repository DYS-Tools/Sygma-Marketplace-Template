 // var elements = document.getElementsByClassName(names);
 var loader = document.getElementById("cssload-thecube");
 var application = document.getElementById("content-application-front");

 var start = 0
 var end = 0
 var diff = 0
 var end = new Date()
 var diff = end - start

 /*
 if(diff.getMilliseconds() <= 250){
    loader.style.display = "block";
    application.style.display = "none";
}
*/

 // Fonction call is DOM is charged
 document.addEventListener('DOMContentLoaded', function(){
     diff = new Date(diff)
     var msec = diff.getMilliseconds()
     console.log('page chargée en ' + msec + ' ms');

     loader.style.display = "none";
     application.style.display = "block";
 });