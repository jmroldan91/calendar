(function(){
    
    /*global angular*/
    var months = [
        {},/*array de 0 vacio*/
    	{value:1,text:"Enero"},
    	{value:2,text:"Febrero"},
    	{value:3,text:"Marzo"},
    	{value:4,text:"Abril"},
    	{value:5,text:"Mayo"},
    	{value:6,text:"Junio"},
    	{value:7,text:"Julio"},
    	{value:8,text:"Agosto"},
    	{value:9,text:"Septiembre"},
    	{value:10,text:"Octubre"},
    	{value:11,text:"Noviembre"},
    	{value:12,text:"Diciembre"}
	];
	
    
    var app = angular.module('calendar', []);
    var user = {};
    function stringToDate(string){
	    var dt  = string.split(/\-|\s/);
	    return new Date(dt.slice(0,3).join('/')+' '+dt[3]);
	}
    app.controller('ctlFormTask', ['$http', function($http){
        this.task = {};
        this.addTask = function(tasks, activeDay){
            var that = this;
            $http.get('controller.php?op=insert&table=task&idUser='+user.email+'&title='+this.task.title+'&content='+this.task.content+'&taskdate='+activeDay).success(function(data) {
                if(data.result != -1){
                    that.task.id = data.result;
                    that.task.idUser = user.email;
                    that.task.taskdate = activeDay;
                    tasks.push(that.task);
                    that.task = {};
                    $('.modal').modal('hide');
                }else{
                    console.log(JSON.stringify(data.result));
                }
            });
        };
        this.setTask = function(tasks){
            var that = this;
            $http.get('controller.php?op=set&table=task&pkid='+this.task.id+'&id='+this.task.id+'&idUser='+user.email+'&title='+this.task.title+'&content='+this.task.content+'&taskdate='+this.task.taskdate).success(function(data) {
                if(data.result != -1){
                    var pos=0;
                    while(that.task.id != tasks[pos].id){
                        pos++;
                    }
                    for(var prop in tasks[pos]){
                        tasks[pos][prop] = that.task[prop];
                    }
                    that.task = {};
                    $('#editForm').modal('toggle');
                }else{
                    console.log(JSON.stringify(data.result));
                }
                
            });
        };
    }]);
    app.directive('tasksList', function(){
       return {
         restrict: 'E',
         templateUrl: 'tpl/tasks-list.html'
       }; 
    });
    app.directive('calendarControls', function(){
       return {
         restrict: 'E',
         templateUrl: 'tpl/calendar-controls.html'
       }; 
    });
    app.directive('calendarView', ['$http', function($http) {
        return {
            restrict: 'E',
            templateUrl: 'tpl/calendar-view.html',
            controller: function(){
                var that = this;
                this.today = new Date(); 
                this.the_year = this.today.getFullYear();
                this.the_month = this.today.getMonth()+1;
                this.the_day = this.today.getDate();
                this.firstDayOfMonth = "";
                this.lastDayOfMonth = "";
                this.month = [];
                this.months = months;
                this.activeDay = new Date(this.the_year, this.the_month-1, this.the_day).toISOString().slice(0, 19).replace('T', ' ');
                this.hours = [
                    {'h': '8' , 'v' : '8.15 - 9.15'},
                    {'h': '9' , 'v' : '9.15 - 10.15'},
                    {'h': '10' , 'v' : '10.15 - 11.15'},
                    {'h': '12' , 'v' : '11.45 - 11.45'},
                    {'h': '13' , 'v' : '12.45 - 12.45'},
                    {'h': '14' , 'v' : '13.45 - 13.45'},
                ];
                //tasks
                this.nrpp = 5;
                this.page = 1;
                this.pages = 1;
                this.tasks = [];
                //user 
                this.createMonth = function () {
                    this.month = [];
                    this.firstDayOfMonth = new Date(this.the_year, this.the_month-1, 1);
                    this.lastDayOfMonth = new Date(this.the_year, this.the_month, 0);
                    var c=1;
                    var week = new Array();
                    while(c < this.firstDayOfMonth.getDay()){
                        week.push( { 'type' : 'bg-warning', 'number' : '-'} );
                        c++;
                    }
                    var day = 1;
                    while(day <= this.lastDayOfMonth.getDate()){
                        if(c < 7){
                            if((day == this.today.getDate()) && (this.the_month == (this.today.getMonth()+1)) && (this.the_year == (this.today.getFullYear()))){
                                week.push( { 'type' : 'day bg-info', 'number' : day} );
                            }else{
                                week.push( { 'type' : 'day bg-success', 'number' : day} );
                            }
                        }else{
                            if((day == this.today.getDate()) && (this.the_month == (this.today.getMonth()+1)) && (this.the_year == (this.today.getFullYear()))){
                                week.push( { 'type' : 'day bg-info', 'number' : day} );
                            }else{
                                week.push( { 'type' : 'day bg-success', 'number' : day} );
                            }
                            this.month.push(week);
                            week = new Array();
                            c=0;
                        }
                        day++;
                        c++;
                    }
                    var ud = week.length;
                    if(ud != 0){
                        while(ud < 7){
                            week.push( { 'type' : 'bg-warning', 'number' : '-'} );
                            ud++;
                        }
                        this.month.push(week);
                    }
                };
                this.next = function() {
                    if(this.the_month == 12){
                        this.the_month = 1;
                        this.the_year++;
                    }else{
                        this.the_month++;
                    }
                    this.createMonth();
                    this.addMark();
                    $('#tasksList').hide();
                };
                this.prev = function(){
                    if(this.the_month == 1){
                        this.the_month = 12;
                        this.the_year--;
                    }else{
                        this.the_month--;
                    }
                    this.createMonth();
                    this.addMark();
                    $('#tasksList').hide();
                };
                this.setMonth = function(m){
                    this.the_month = m;
                    this.createMonth();
                    this.addMark();
                    $('#tasksList').hide();
                };
                this.setYear = function(y){
                    if(y*1 == y && y > 1900 && y < 2099){
                        this.the_year = y;
                        this.createMonth();
                        this.addMark();
                        $('#tasksList').hide(); 
                        }
                };
                this.selectDay = function(d){
                    if(d != '-'){
                        $('.selected').removeClass('selected');
                        $('#d_'+d).filter('.day').addClass('selected');
                        $('#tasksList').fadeOut('slow');
                        this.the_day = d;
                        this.activeDay = new Date(this.the_year, this.the_month-1, this.the_day+1).toISOString().slice(0, 19).replace('T', ' ');
                        this.getTaskPage(this.page);
                        $('#tasksList').fadeIn(1500);
                    }
                };
                this.setHour = function(h) {
                    this.activeDay = new Date(this.the_year, this.the_month-1, this.the_day, ++h).toISOString().slice(0, 19).replace('T', ' ');
                };
                this.getTaskPage = function(page) {
                    if(!page) page = this.page;
                    $http.get('controller.php?op=read&table=task&page='+this.page+'&nrpp='+this.nrpp+'&campo=taskdate&filter='+this.activeDay.substr(0, 10)).success(function(data) {
                        that.tasks = JSON.parse(data.resultset);
                        that.pages = Math.ceil(data.pages/that.nrpp);
                    });
                };
                this.editTask = function(task){
                    var tmp = JSON.parse(JSON.stringify(task));
                    console.log(JSON.stringify(tmp));
                    angular.element(document.getElementById('taskFormedit')).scope().ctlForm.task = tmp;
                    $('#editForm').modal('toggle');
                 };
                this.del = function(id){
                    console.log(id);
                    $http.get('controller.php?op=delete&table=task&pkid='+id).success(function(data) {
                        if(data.result != -1){
                        var pos = 0;
                        while(that.tasks[pos].id != id){
                            pos++;
                        }
                        that.tasks.splice(pos, 1);
                        }else{
                            console.log(JSON.stringify(data.result));
                        }
                    });
                };
                this.nextPage =function(){
                    this.page = Math.min(this.pages, ++this.page);
                    this.getTaskPage();
                };
                this.prevPage =function(){
                    this.page = Math.max(1, --this.page);
                    this.getTaskPage();
                };
                this.showDesc = function($event){
                    $event.stopPropagation();
                    $($event.target).find('.taskDesc').slideDown();
                };
                this.hideDesc = function($event){
                    $event.stopPropagation();
                    $($event.target).find('.taskDesc').slideUp();
                };
                this.filterFn = function(task){
                    var date = stringToDate(task.taskdate);
                    return +date.getHours(); 
                };
                this.filterHour = function(task, hour){
                    var date = stringToDate(task.taskdate);
                    return +date.getHours() == hour.h;
                };
                this.addMark = function(){
                    $http.get('controller.php?op=getDaysWhitTasks&m='+this.the_month).success(function(data) {
                        var r = data.days;
                        for(var key in r){
                            $('#d_'+r[key]).addClass('withTask');
                        }
                    });
                };
                this.createMonth();
                this.addMark();
                $('#tasksList').hide(); 
            },
            controllerAs: 'ctrlCalendar'
        };
    }]);
    app.directive('userLogin', ['$http', function($http) {
        return {
            restrict: 'E',
            templateUrl: 'tpl/user-login.html',
            controller: function(){
                this.user = {}; 
                this.regex = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;
                this.login = function() {
                    var that = this;
                    $http.get('controller.php?op=login&login='+this.user.email+'&pass='+this.user.pass+'&table=user').success(function(data) {
                        if(data.type == 'success'){
                            that.user = data.user;
                            $('#lView').hide();
                            $('#logout').removeClass('hidden');
                            $('#logout').show();
                            $('#cView').removeClass('hidden');
                            $('#cView').show();
                            user = that.user;
                        }else{
                            $('#login-msg').text('Error de inicio de sesión');
                        }
                    });
                };
                this.logout = function() {
                    var that = this;
                    $http.get('controller.php?op=loguot').success(function(data) {
                        that.user = {};
                        user = {};
                        $('#lView').show();
                        $('#logout').hide();
                        $('#cView').hide();
                    });
                };
                this.checSession = function(){
                    var that = this;
                    $http.get('controller.php?op=checkSession').success(function(data) {
                        if(data.type == 'success'){
                            that.user = data.user;
                            $('#lView').hide();
                            $('#logout').removeClass('hidden');
                            $('#logout').show();
                            $('#cView').removeClass('hidden');
                            $('#cView').show();
                            user = that.user;
                        }
                    });
                }
                this.checSession();
            },
            controllerAs: 'ctrlUser'
        };
    }]);
})();

