function AppendNewMessage(Message,thumb,bubbleColor,Time,SeenState){
	
// Seen State Change Icon
if(SeenState == "waiting"){
	var LastSeenIcon = "img/b/time.png";
}else{
	var LastSeenIcon = "img/b/error.png";
}

	
	$("#SingleMessageBody").append('\
	<div class="SingleMessageCell">\
		<thumb class="thumb" style="background-image:url('+thumb+');"></thumb>\
		<MessageContent style="background-color:'+bubbleColor+';">'+Message+'</MessageContent>\
		<MessageInfoBox>\
		<messageState><img src="'+LastSeenIcon+'" /></messageState>\
		<messageTime>'+Time+'</messageTime>\
		</MessageInfoBox>\
	</div>\
	');
	
}