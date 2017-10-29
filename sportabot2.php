<?php
//this is an example chat bot in php
//it's a lazy town character, but the bot is dumb so it only has a few responses
//the group me chat api sends POST messages in json to a url
//from this i grab the name of the user and the message itself to parse for keywords
//then we send a message back to group me's api with our message

//function to get messages from POST
function get_message(){
  //get the raw data from the POST request
  $raw_post = file_get_contents("php://input");
  //PHP has a bult in json parser but its very pick so i parse the json here real quick
  $raw_post = str_replace("\"","",$raw_post);
  $raw_post = substr($raw_post, 1,strlen($raw_post)-2);
  echo $raw_post;
  $raw_post = explode(",",$raw_post);
  $post = [];
  foreach($raw_post as &$postelem){
    $attr = explode(":",$postelem);
    $post[$attr[0]] = $attr[1];
  }
  return $post;
 }

//function to send a message
function send($msg, $bid){
  echo "<br>$msg<br>$bid";
  $url = 'https://api.groupme.com/v3/bots/post';
  //there are probably more options see groupme api for details
  $data_string = '{"text":"'.$msg.'","bot_id":"'.$bid.'"}';
  echo "<br>$data_string";
  //send a post request with our json message
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string)
  ));
  $result = curl_exec($ch);
}

//this just a helper function to clean things upp
function contains($message, $str){
  return (strpos($message, $str) !== false);
}

//you can get your own bot id from https://dev.groupme.com/bots
//i dont have one here because i dont want random people on the internet sending messages from my bot
$botid = "<groupmebotid>";
//if you name your bot somthing else in the groupme diolog put the name here
$bot_name = "sportabot";

//parse information from  message
$msg = get_message();
echo "<br>";
echo var_dump($msg);
$name = $msg["name"];
$message = $msg["text"];

//check to make sure the message is not from the bot itself, you can remove this if statement for some fun results
//for the most part checking for the name to not be group me blocks information messages
//but the bot still sometimes reacts to notifications about the time
if($name != "sportabot" && $name != "GroupMe"){
  //if you change your nickname to echo it'll repeat everything you say
  if($name=="echo"){
    send($message, $botid);
  }else if(contains($message, "candy")||contains($message, "candy")){
    send("you should probably eat something healthier than candy @$name", $botid);
  }else if(contains($message, "Bot")||contains($message, "bot")){
    //the most important part of writing a bot is to make it deny that its a bot
    send("im not a bot@$name I'm number ten. My name is Sportacus.", $botid);
  }else if((contains($message, "Number")||contains($message, "number"))
         &&(contains($message, "Nine")||contains($message, "nine")||contains($message, "9"))){
    //this is an easter egg my friend had me put in
    send("https://www.youtube.com/watch?v=E-fSP1y0RAo",$botid);
  }else if(contains($message, "sports")||contains($message, "Sports")||contains($message, "sport")
         ||contains($message, "Sport")){
    send("i love sports @$name", $botid);
  }
}
?>
