<!DOCTYPE html>
<html>

  <head>
    <link rel="shortcut icon" href="images/favicon.png">
  </head>


  <body>

      <form id='add_player_form' method="post" action="">
  			<input type='text' class='add_player_name' placeholder='RSN to add'/>
  			<input type='submit' class='add_player_submit' value='Check'/>
  		</form>
  		<div class='add_output'>

  		</div>

      <form id='del_player_form' method="post" action="">
        <input type='text' class='del_player_name' placeholder='RSN to del'/>
        <input type='submit' class='del_player_submit' value='Check'/>
      </form>
      <div class='del_output'>
      </div>

      <form id='ban_player_form' method="post" action="">
        <input type='text' class='ban_player_name' placeholder='RSN to ban/del'/>
        <input type='submit' class='ban_player_submit' value='Check'/>
      </form>
      <div class='ban_output'></div>

      <form id='rename_player_form' method="post" action="">
        <input type='text' class='rename_player_name1' placeholder='RSN to change FROM'/>
        <input type='text' class='rename_player_name2' placeholder='RSN to change TO'/>
        <input type='submit' class='rename_player_submit' value='Check'/>
      </form>
      <div class='rename_output'>
      </div>

   <script src='https://code.jquery.com/jquery-3.0.0.min.js'></script>
   <script src='call.js'></script>
  </body>
</html>
