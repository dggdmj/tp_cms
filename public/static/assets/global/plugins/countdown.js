var countdown=function(year,month,day,hour,minute,second){	//倒计时的计算方法，参数（年，月，日，时，分，秒）
	var now = new Date();	//当前日期
	var endDate = new Date(year,month-1,day,hour,minute,second);	//结束日期
	var leftTime=endDate.getTime()-now.getTime();	//相差的日期
	var leftsecond = parseInt(leftTime/1000);	//把相差的日期转换为秒数
	//console.log(leftsecond);
	var cday=Math.floor(leftsecond/(60*60*24));	//相差的天数
	var chour=Math.floor((leftsecond-cday*24*60*60)/3600); //相差的小时数
	var cminute=Math.floor((leftsecond-cday*24*60*60-chour*3600)/60);	//相差的分钟数
	var csecond=Math.floor(leftsecond-cday*24*60*60-chour*3600-cminute*60);	//相差的秒数
	//console.log(cday+"天"+chour+"时"+cminute+"分"+csecond+"秒");
	if(leftsecond<=0){	//当倒计时结束就返回结束的标识
		return {"day":cday,"hour":chour,"minute":cminute,"second":csecond,"over":true};
	}else{
		return {"day":cday,"hour":chour,"minute":cminute,"second":csecond,"over":false};
	}
}