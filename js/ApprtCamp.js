$(document).ready(function(){
       getHomeTweets();
       
       $('.home').click(function(e){
          e.preventDefault();
          getHomeTweets();
       });
       
        $('.ajax_class').click(function(e){
            e.preventDefault();
            var user_name= $(this).text();
            getUserTweets(user_name);
        });
       
    $("#follower").typeahead({
        minLength: 1,
        source: function(query, process) {
            $.post('core/SearchFollowers.php',{
                q: query
            }, function(data) {
                process(JSON.parse(data));
            });
        },
        updater: function (user_name) {
            getUserTweets(user_name);
        }
    });
});

function getHomeTweets()
{
   
    $.ajax({
          url:'core/getHomeTweets.php',
          success: function(data){
              $('#slideshow').html(data);
              slide();
          },
          error: function(data){
               $('#slideshow').html(data);
              alert("Some error occured "+data);             
          }
      });
}
function getUserTweets(user_name)
{
  
    $.ajax({
        url: "core/getUserTimelineTweets.php",
        type: "POST",
        data: {
            'follower':  user_name.toString()
        },
        success: function(data){
            $('#slideshow').html(data);
            slide();
        },
        error : function(data){
            alert("some error occured"+data);
           $('#slideshow').html(data);
        }
    });
}

function slide()
{
     $('#myCarousel').carousel({});
    
}



