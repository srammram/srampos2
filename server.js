var socket  = require( 'socket.io' );
var express = require('express');
var app     = express();
var server  = require('http').createServer(app);
var io      = socket.listen( server );
//var fp = require("find-free-port")
//fp(6000,6500, function(err, freePort){
//  console.log(freePort);
//});
var op = require('openport');
op.find(
  {
    startingPort: 6401,
    endingPort: 6500,
    //avoid: [ 6400, 1500 ]
  },
  function(err, port) {
    if(err) { console.log(err); return; }
    //console.log(port);
    // yea! we have an open port between 1024 and 2000, but not port 1025 or 1500.
 
/*getPort = require('get-port');console.log(getPort)*/;


//var mysql = require("mysql");

var mysql = require("mysql");
var instance_name = 'srampos';
var dbcon = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "07082019"
});

var port    = process.env.PORT || port;
var request = require('request');


dbcon.connect(function(err){
	if(err){
		console.log('Error connecting to Db');
		return;
	}
	console.log('Database Connection established');
	
});
dbcon.query("SELECT socket_port,socket_host FROM srampos_settings ", function(err,settings){
    if(err){
    }else{
	socket_host = settings[0].socket_host;
	
    }
});
dbcon.query("update srampos_settings set socket_port="+port, function(err,settings){
  //console.log(socket_host+'/'+instance_name+'/notify/updatesocketjs')
  request.post({
	url:socket_host+'/'+instance_name+'/notify/updatesocketjs',
	form: {port:port}},
	function(err,httpResponse,body){
	  var responseData = { msg:'Request Recieved'};
	  
	}
      );
});

//var dbcon = require('./serverdb');
server.listen(port, function () {
  console.log('Server listening at port %d', port);
});
var socket_host = '';
io.on('connection', function (socket) {

    ////////////// Users Socket IDs ///////////////////
    socket.on('error', (error) => {
	console.log('Error!');
	server.listen(port, function () {
  console.log('Server listening at port %d', port);
});
  
});
  socket.on('user_socket', function(data){
	console.log('User Socket');
	console.log(data)
	var user_id = data.user_id;
	var group_id = data.group_id;
	var device_type = data.device_type;	
	var device_imei = data.device_imei;
	//var device_token = data.device_token;
	var socket_id = socket.id;	
	//socket.emit('testingemit', {
	//    title:'This is testing msg',
	//},function(confirmation){
	//    console.log('testing........');
	//    console.log(confirmation);
	//});
	if(user_id==''){
	  user_id = 0;
	}
	var d = new Date();
	$createdDate = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
	console.log($createdDate);
	$modifiedDate= '0000-00-00 00:00:00';
	var device_table = 'srampos_device_detail';	
	dbcon.query("SELECT socket_id FROM "+device_table+" AS d WHERE d.user_id = '"+user_id+"' AND d.group_id = "+group_id, function(err,user){
		if(err){
			 console.log(err);
		}else{
			console.log(user)
			if(user!=''){
			  $modifiedDate = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
				dbcon.query("UPDATE "+device_table+" SET devices_key = '"+device_imei+"',socket_id = '"+socket_id+"',modified = '"+$modifiedDate+"'  WHERE user_id = '"+user_id+"'  AND group_id = '"+group_id+"'" );
				console.log("User Updated"+device_imei+socket_id);
			}else{
				dbcon.query("INSERT INTO "+device_table+"  (user_id, group_id,device_type, socket_id, devices_key,created) VALUES ('"+user_id+"', '"+group_id+"','"+device_type+"', '"+socket_id+"', '"+device_imei+"', '"+$createdDate+"') ");
				console.log("User Inserted");
				
			}
		}
	});	
    });
    
    
    socket.on('customer_socket', function(data){
	console.log('Customer Socket');		 
	var table_id = data.table_id;
	var device_type = data.device_type;
	var device_imei = data.device_imei;
	//var device_token = data.device_token;
	var socket_id = socket.id;
	var d = new Date();
	$createdDate = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
	$modifiedDate= '0000-00-00 00:00:00';
	var device_table = 'srampos_table_device_detail';	
	dbcon.query("SELECT socket_id FROM "+device_table+" AS d WHERE d.table_id = "+table_id+" AND d.device_type = '"+device_type+"' AND d.devices_key = '"+device_imei+"' ", function(err,user){
		if(err){
			 console.log(err);
		}else{
			//console.log(user)
			if(user!='' && user[0].socket_id != null){
			  $modifiedDate = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
				dbcon.query("UPDATE "+device_table+" SET socket_id = '"+socket_id+"',modified = '"+$modifiedDate+"' WHERE table_id = '"+table_id+"'  AND devices_key = '"+device_imei+"' AND device_type = '"+device_type+"'" );
				console.log("Customer Updated");
			}else{
				dbcon.query("INSERT INTO "+device_table+"  (table_id, device_type,socket_id, devices_key,created) VALUES ('"+table_id+"','"+device_type+"','"+socket_id+"', '"+device_imei+"', '"+$createdDate+"') ");
				console.log("Customer Inserted");
				
			}
		}
	});	
    });
    
    
    socket.on('read_notification', function(data){
	//console.log(data);		 
	var to_user_id = data.user_id;
	var table_id = data.table_id;
	//var msg = data.msg;
	//var type = data.title;
	var bbq_code  = data.bbq_code;
	var notify_id  = data.notify_id;
	var socket_id = socket.id;	
	var date = new Date();
	
	var tag = 'bbq-cover-validation';
	var table_name = 'srampos_notiy';	
	dbcon.query("UPDATE "+table_name+" SET is_read = '1' WHERE to_user_id = '"+to_user_id+"' AND id = '"+notify_id+"' AND split_id = '"+bbq_code+"' AND tag = '"+tag+"' AND table_id = '"+table_id+"' " , function(err,user){
	    if(err){
		console.log(err)
	    }else{
		console.log('success');
	    }
	});
					
    });
    socket.on('read_bbqreturn_notification', function(data){
	//console.log(data);		 
	var to_user_id = data.user_id;
	var table_id = data.table_id;
	//var msg = data.msg;
	//var type = data.title;
	var bbq_code  = data.bbq_code;
	var socket_id = socket.id;	
	var date = new Date();
	var tag = 'bbq-return';
	var table_name = 'srampos_notiy';	
	dbcon.query("UPDATE "+table_name+" SET is_read = '1' WHERE to_user_id = '"+to_user_id+"' AND split_id = '"+bbq_code+"' AND tag = '"+tag+"' AND table_id = '"+table_id+"' " , function(err,user){
	    if(err){
		console.log(err)
	    }else{
		console.log('success');
	    }
	});
					
    });
    socket.on( 'update_table', function( data ) {
	io.sockets.emit( 'update_table', {
	    update: 1,
	    tableid:data.tableid,
	    socketid:socket.id
	});
    });
    socket.on( 'update_bbqtable', function( data ) {
      io.sockets.emit( 'update_bbqtable', {
	  update: 1,
	  tableid:data.tableid,
      });
    });
    socket.on( 'update_orders', function( data ) {
      io.sockets.emit( 'update_orders', {
	  update: 1,
      });
    });
    socket.on( 'update_kitchen_orders', function( data ) {
      io.sockets.emit( 'update_kitchen_orders', {
	  update: 1,
      });
    });
    socket.on( 'notification', function( data ) {
      io.sockets.emit( 'notification', {
	  title:data.title,
	  msg:data.msg
      });
    });
	
	socket.on( 'pro_notification', function( data ) {
		console.log(socket.id);
		console.log('dddd');
		console.log(data);
		
		io.sockets.emit('pro_notification',  'Hello World.');
	});
	
    socket.on('push_notification', function( data ) {
	console.log('bbq bill socketId...'+data.socket_id)
	io.to(data.socket_id).emit( 'push_notification', {
	    title:data.title,
	    msg:data.msg,
	    type:data.type,	
	    socketid:data.socket_id
	});
    });
    socket.on('error', function (err) {
	console.log('Error')
    console.log(err);
});
    socket.on('bbq_push_notification', function( data ) {
	console.log('bbq_push_notification');
	console.log(data);
	//console.log('BBQ:'+data.bbq_code+' User id:'+data.user_id+' table id:'+data.table_id);
	io.to(data.socket_id).emit( 'bbq_push_notification', {
	    title:data.title,
	    msg:data.msg,
	    type:data.type,
	    bbq_code:data.bbq_code,
	    notify_id:data.notify_id,
	    table_id:data.table_id,
	});
    });
    socket.on('billRequest_push_notification', function( data ) {
      console.log('billRequest_push_notification')
	console.log(data)
	io.to(data.socket_id).emit( 'billRequest_push_notification', {
	    title:data.title,
	    msg:data.msg,
	    split_id:data.split_id,
	    notify_id:data.notify_id,
	    table_id:data.table_id,
	    request_type:data.request_type,
	    type:data.sale_type,
	});
    });
    socket.on('bbq_return_push_notification', function( data ) {
	console.log('bbq_return_push_notification')
	console.log(data)
	io.to(data.socket_id).emit( 'bbq_return_push_notification', {
	    title:data.title,
	    msg:data.msg,
	    //type:data.type,
	    bbq_code:data.bbq_code,
	    table_id:data.table_id,
	});
    });
    socket.on('customer_push_notification', function( data ) {
	io.to(data.socket_id).emit( 'customer_push_notification', {
	    title:data.title,
	    msg:data.msg,
	    socketid:data.socket_id
	});
    });
    //socket.broadcast.to(data.socketid).emit('bbq_cover_validation', {
    //	title:data.title,
    //	msg:data.msg
    //    });  
    socket.on( 'testing', function( data,fn) {
	console.log(data)
	var responseData = { msg:'Requestyyyyy Recieved'};
	fn(responseData);
    });
    socket.emit( 'testingemit', {
	title:'This is testing msg',
      },function(confirmation){
	console.log(confirmation)
    });
	
    socket.on( 'bbq_cover_validation', function( data,fn) {
      console.log(data);
      request.post({
		url:socket_host+'/'+instance_name+'/notify/bbqnotification',
		form: {user_id:data.user_id,group_id:data.group_id,table_id:data.table_id,warehouse_id:data.warehouse_id,bbq:data.bbq_code, stop_count:data.stop_count}},
		function(err,httpResponse,body){
		  var responseData = { msg:'Request Recieved'};
		  fn(responseData);
		}
      );
    });
	
	socket.on( 'bbq_cover_validation_stop', function( data) {
      console.log(data);
	  console.log('bbq_cover_validation Stop Emit');
      request.post({
		url:socket_host+'/'+instance_name+'/notify/bbqnotification_stop',
		form: {bbq:data.bbq_code, stop_count:data.stop_count}});
    });
	
    socket.on( 'bbq_return_request', function( data) {
      console.log('bbq return notification:');
      console.log(data);
      request.post({
	url:socket_host+'/'+instance_name+'/notify/bbqreturn_notification',
	form: {user_id:data.user_id,group_id:data.group_id,table_id:data.table_id,warehouse_id:data.warehouse_id,bbq:data.bbq_code}}
      );
    });
	
	socket.on( 'bbq_return_request_stop', function( data) {
      console.log('bbq_return_request Stop Emit');
      console.log(data);
      request.post({
			url:socket_host+'/'+instance_name+'/notify/bbqreturn_notification_stop',
			form: {bbq:data.bbq_code, stop_count:data.stop_count}}
      );
    });
	
	
    socket.on( 'bill_request', function( data) {
      console.log('bill request notification:');
      console.log(data);
      request.post({
		url:socket_host+'/'+instance_name+'/notify/billRequestNotification',
		form: {split_id:data.split_id, stop_count:data.stop_count}}
      );
    });
	
	socket.on( 'bill_request_stop', function( data) {
      console.log('bill request notification Stop Emit');
      console.log(data);
      request.post({
		url:socket_host+'/'+instance_name+'/notify/billRequestNotification_stop',
		form: {split_id:data.split_id, stop_count:data.stop_count}}
      );
    });
	
    socket.on( 'payment_request', function( data) {
      console.log('payment request notification:');
      console.log(data);
      request.post({
	url:socket_host+'/'+instance_name+'/notify/paymentRequestNotification',
	form: {split_id:data.split_id}}
      );
    });
    socket.on( 'bbq_cover_confirmed', function( data ) {
	console.log(data.socket_id)
      io.to(data.socket_id).emit( 'bbq_cover_confirmed', {
	  title:data.title,
	  msg:data.msg,
	  user_id:data.user_id,
	  bbqcode:data.bbqcode
	}/*,function(confirmation){
	    console.log('testing........');
	    console.log(confirmation);
	}*/	
      );
    });
  
  //io.sockets.in('BBQ123').emit('new_msg', {msg: 'hello'});
});
 }
);
