class MulticoinBlock extends HTMLElement {
	constructor() {
		super();

		this.clicks = 0;
		this.maxClicks = 10;

		this.blockSvgUse = this.querySelector('.multicoin-block__block use');

		this.bind();
	}

	bind() {
		this.addEventListener('click', (e) => {
			if (this.clicks >= this.maxClicks) {
				return;
			}

			this.clicks++;
			this.addCoin();
			this.style.setProperty('animation-name', 'bounce');

			if (this.clicks >= this.maxClicks) {
				this.blockSvgUse.setAttribute('href', '#empty');
				function animation() {
					document.getElementById('main').style.display = "none";
					document.getElementById('animation').style.display = "flex";

					/**
					 * Created by Administrator on 2015/6/24.
					 */
					(function() {
						var imageRoot = "https://raw.githubusercontent.com/ysj16/mario/master/images/"
						var resourceCache = {};
						var loading = [];
						var readyCallbacks = [];

						// Load an image url or an array of image urls
						function load(urlOrArr) {
							if(urlOrArr instanceof Array) {
								urlOrArr.forEach(function(url) {
									_load(url);
								});
							}
							else {
								_load(urlOrArr);
							}
						}

						function _load(url) {
							if(resourceCache[url]) {
								return resourceCache[url];
							}
							else {
								var img = new Image();
								img.onload = function() {
									resourceCache[url] = img;

									if(isReady()) {
										readyCallbacks.forEach(function(func) { func(); });
									}
								};
								resourceCache[url] = false;
								img.src = imageRoot + url;
							}
						}

						function get(url) {
							return resourceCache[url];
						}

						function isReady() {
							var ready = true;
							for(var k in resourceCache) {
								if(resourceCache.hasOwnProperty(k) &&
									!resourceCache[k]) {
									ready = false;
								}
							}
							return ready;
						}

						function onReady(func) {
							readyCallbacks.push(func);
						}

						window.resources = {
							load: load,
							get: get,
							onReady: onReady,
							isReady: isReady
						};
					})();

					/**
					 * Created by yu on 2015/6/24.
					 */
					//???????????????
					function Control(){
						this.code = { 37: 'left', 39: 'right', 38: 'jump', 40: 'down'}
						this.status = { 'left': false, 'right': false, 'jump': false, 'down': false };
						document.addEventListener("keydown",function(e){
							var status = this.code[e.keyCode];
							if(!status) return;
							this.status[status] = true;
						}.bind(this))
						document.addEventListener("keyup",function(e){
							var status = this.code[e.keyCode];
							if(!status) return;
							this.status[status] = false;
						}.bind(this))

					}
					//???????????????
					function Map(img,size){
						this.background = resources.get("background1.png")
						this.x=0;
					}
					Map.prototype.update = function(control,player,canvas,interTime){
						if(control.status.right){
							if(player.position.x-this.x>=canvas.width/2)
								this.x += player.speed.x*interTime/1000;
						}
					}

					//?????????
					function Model(imgs,position){
						this.imgs = deepClone(imgs);
						this.position = position;
						this.act = "default";
					}
					Model.prototype.alls = [];//?????????????????????????????????????????????????????????????????????
					Model.prototype.map;//???????????????
					//???????????????????????????
					function Livings(imgs,position){
						Model.call(this,imgs,position)
						this.crush = {left:false,right:false,top:false,bottom:false};
						this.speed = {x:-30,y:0};
						this.act="default";
						this.isAlive = true;
					}
					inheritPrototype(Livings,Model)
					Livings.prototype.spirit = function(act){
						this.imgs[act].x += this.imgs[act].spiritW;
						if(this.imgs[act].x == this.imgs[act].img.width) this.imgs[act].x=0;

					}
					Livings.prototype.move = function(x,y){
						if(this.isAlive) this.collide(Livings.prototype.onCrush);
						if(x>0&&!this.crush.right)
							this.position.x += x;
						else if(x<0&&!this.crush.left)
							this.position.x += x;
						if(y>0&&!this.crush.bottom||!this.isAlive)
							this.position.y +=y;
						else if(y<0&&!this.crush.top)
							this.position.y +=y;
						//console.log(this.speed)
					}
					Livings.prototype.autoMove = function(interTime){
						//if(this.crush.left) this.speed.x = -this.speed.x;
						this.move(this.speed.x*interTime/1000,0);
						this.spirit("default");
						this.gravity(400, interTime / 1000)
						//console.log(this.imgs["default"])
					}
					Livings.prototype.die =function(){
						if(this.isAlive) {
							this.isAlive = false;
							this.act= "die";
						}
					}
					Livings.prototype.gravity = function(g,interTime){//????????????
						if(this.position.y<CHEIGHT+100&&!this.crush.bottom||!this.isAlive){
							this.move(0,this.speed.y*interTime + g*interTime*interTime/2);
							this.speed.y += g*interTime;
						}
					}
					Livings.prototype.collide = function(callback){//????????????
						var tImg = this.imgs[this.act],
							tCenter = {x:this.position.x + tImg.renderW/2,y:this.position.y + tImg.renderH/2},
							that = this;
						var tCrushW = tImg.crushW||tImg.renderW,
							tCrushH = tImg.crushH||tImg.renderH;
						this.crush = {left:false,right:false,top:false,bottom:false};
						this.alls.forEach(function(model,index){
							if(model!==that) {
								var mImg = model.imgs[model.act],
									mCrushW = mImg.crushW || mImg.renderW,
									mCrushH = mImg.crushH || mImg.renderH;
								var mCenter = {x: model.position.x + mImg.renderW / 2, y: model.position.y + mImg.renderH / 2};
								if (Math.abs(tCenter.x - mCenter.x) < (tCrushW / 2 + mCrushW / 2) && Math.abs(tCenter.y - mCenter.y) < (tCrushH / 2 + mCrushH / 2)+2) {
									//if(model.constructor==Player) console.log(tCenter.y - mCenter.y > 0, tCenter.y - mCenter.y <= tCrushH / 2 + mCrushH / 2  , Math.abs(tCenter.x - mCenter.x) < (tCrushW / 2 + mCrushW / 2)-12)
									if (tCenter.x - mCenter.x > 0 && tCenter.x - mCenter.x < tCrushW / 2 + mCrushW / 2 && Math.abs(tCenter.y - mCenter.y) < (tCrushH / 2 + mCrushH / 2)) {
										that.crush.left = true;
									}
									if (tCenter.x - mCenter.x < 0 && mCenter.x - tCenter.x < tCrushW / 2 + mCrushW / 2 && Math.abs(tCenter.y - mCenter.y) < (tCrushH / 2 + mCrushH / 2)) {
										that.crush.right = true;
									}
									if (tCenter.y - mCenter.y > 0 && tCenter.y - mCenter.y <= tCrushH / 2 + mCrushH / 2  && Math.abs(tCenter.x - mCenter.x) < (tCrushW / 2 + mCrushW / 2)-12) {
										that.crush.top = true;
										that.speed.y = 0;
										that.position.y +=1;
									}
									if (tCenter.y - mCenter.y < 0 && mCenter.y - tCenter.y < tCrushH / 2 + mCrushH / 2+2 && Math.abs(tCenter.x - mCenter.x) < (tCrushW / 2 + mCrushW / 2)-12) {
										that.crush.bottom = true;
										that.position.y = model.position.y-tImg.renderH;//?????????????????????????????????
									}

									that.onCrush&&that.onCrush(model);

								}
							}

						})
					}
					//???????????????????????????
					function Player(imgs,position,callbacks){
						Livings.call(this,imgs,position);
						this.speed = {x:80,y:0};
						this.lifes = 3;
						this.act="moveR";
						this.onCrush = callbacks.onCrush
					}
					inheritPrototype(Player,Livings);
					Player.prototype.die =function(){
						if(this.isAlive) {
							this.isAlive = false;
							this.speed = {x: 0, y: -400};
						}
					}
					Player.prototype.update = function(control,canvas,interTime,loop){//????????????
						var moveX=0,moveY= 0,direction = this.act.slice(-1),ctx = canvas.getContext("2d");
						//???????????????????????????
						if(control.status.left){
							moveX = -this.speed.x * interTime / 1000;
							direction = "L";
							this.spirit("move"+direction)
						}
						if(control.status.right){
							if (this.position.x-this.map.x < canvas.width / 2) {
								moveX = this.speed.x * interTime / 1000;
								direction = "R";
							}
							this.spirit("move"+direction)
						}
						if(control.status.jump){
							if(this.crush.bottom) {
								this.speed.y = -256;
								moveY = this.speed.y * interTime / 1000;
							}
						}
						if(this.crush.bottom){
							this.act = "move"+direction;
						}
						else{
							this.act = "jump"+direction;
						}

						this.move(moveX, moveY)
						this.gravity(400, interTime / 1000)
						if(this.position.y>CHEIGHT){
							this.die();
						}
						if(!this.isAlive){
							loop.info.gameover=true;
						}
						//console.log(this.position)
					}
					//camera??????????????????????????????
					function Camera(canvas){
						this.ctx = canvas.getContext("2d");
					}
					Camera.prototype.drawBackground = function(map){
						var canvas = this.ctx.canvas;
						this.ctx.drawImage(map.background,0,0,709,600,-(map.x%canvas.width),0,canvas.width,canvas.height)
						this.ctx.drawImage(map.background,0,0,709,600,canvas.width-(map.x%canvas.width),0,canvas.width,canvas.height)
					}
					Camera.prototype.drawLivings = function(livings,map){
						var ctx = this.ctx;
						livings.forEach(function(item,index){
							var actImg = item.imgs[item.act];
							var renderW = actImg.renderW||actImg.img.width,
								renderH = actImg.renderH||actImg.img.height,
								spiritW = actImg.spiritW||actImg.img.width;
							//console.log(actImg.img,actImg.x,0,spiritW,actImg.img.height,item.position.x,item.position.y,renderW,renderH)
							ctx.drawImage(actImg.img,actImg.x,0,spiritW,actImg.img.height,item.position.x-map.x,item.position.y,renderW,renderH)
						})
					}
					Camera.prototype.drawModels = function(models,map){
						var ctx = this.ctx;
						models.forEach(function(item,index){
							var actImg = item.imgs[item.act];
							var renderW = actImg.renderW||actImg.img.width,
								renderH = actImg.renderH||actImg.img.height;
							ctx.drawImage(actImg.img,item.position.x-map.x,item.position.y,renderW,renderH)
						})
					}
					Camera.prototype.drawGameInfos = function(infos){
						var ctx = this.ctx;
						if(infos.gameover){
							ctx.font = "30px Arial";
							ctx.fillStyle="#f00";
							ctx.fillText('Game Over',230,180);
						}
					}
					//??????????????????????????????????????????
					function GameLoop(callback,fps){
						var fps = fps||60;
						this.callback = callback;
						this.lastTime = 0;
						this.interval = 1000/fps;
						this.info = {};
					}

					GameLoop.prototype.frame = function(time){
						var interTime = time - this.lastTime;
						if(interTime>this.interval){
							this.callback(interTime)
							this.lastTime = time;
						}
						requestAnimationFrame(this.frame.bind(this))
					}

					//?????????????????????????????????
					function getRenderModels(models,map){
						var arr = [];
						models.forEach(function(item,index){
						if(item.position.x-map.x >= -DEFLENGTH && item.position.x-map.x <= CWIDTH){
							arr.push(item)
						}

						})
						return arr;
					}

					//?????????????????????????????????
					function inheritPrototype(subType,superType){
						var prototype = Object.create(superType.prototype);
						prototype.constructor = subType;
						subType.prototype = prototype;
					}
					//???????????????
					function deepClone(obj){
						var clone = obj.constructor==Object?{}:[];
						for(var i in obj){
							if(obj[i].constructor == Object||obj[i].constructor == Array){
								clone[i] = deepClone(obj[i]);
							}else{
								clone[i] = obj[i]
							}
						}
						return clone;
					}

					/**
					* Created by yu on 2015/6/24.
					*/
					var DEFLENGTH = 40;//??????????????????mario??????????????????
					var CWIDTH = 600;//CANVAS??????
					var CHEIGHT = 400;//CANVAS??????

					resources.load(["background1.png","marioR.png","marioL.png","land1.gif","land2.gif","pipe.png","brick1.gif","marioJR.gif","marioJL.gif","monster.png","monsterD.gif"])
					resources.onReady(function(){
						var canvas = document.getElementById("stage");
						var map = new Map();
						Model.prototype.map = map;
						var camera = new Camera(canvas);
						var control = new Control();//????????????????????????
						var modelsData = [{
							imgs:{
								default:{img:resources.get("land1.gif"),x:0,renderW:DEFLENGTH,renderH:DEFLENGTH}
							},
							position:[{x:0,y:360,width:12,height:1},{x:550,y:360,width:15,height:1},{x:1400,y:360,width:15,height:1}]
						},{
							imgs:{
								default:{img:resources.get("land2.gif"),x:0,renderW:DEFLENGTH,renderH:DEFLENGTH}
							},
							position:[{x:0,y:320,width:12,height:1},{x:550,y:320,width:15,height:1},{x:1400,y:320,width:15,height:1}]
						},{
							imgs:{
								default:{img:resources.get("land1.gif"),x:0,renderW:0.6*DEFLENGTH,renderH:0.6*DEFLENGTH}
							},
							position:[{x:1054,y:296,width:4,height:1},{x:1078,y:272,width:3,height:1},{x:1102,y:248,width:2,height:1},{x:1126,y:224,width:1,height:1}]
						},{
							imgs:{
								default:{img:resources.get("land2.gif"),x:0,renderW:0.6*DEFLENGTH,renderH:0.6*DEFLENGTH}
							},
							position:[{x:1030,y:296},{x:1054,y:272},{x:1078,y:248},{x:1102,y:224},{x:1126,y:200}]
						},{
							imgs:{
								default:{img:resources.get("pipe.png"),x:0,renderW:DEFLENGTH,renderH:1.5*DEFLENGTH}
							},
							position:[{x:430,y:400 -3.5*DEFLENGTH},{x:700,y:400-3.5*DEFLENGTH}]
						},{
							imgs:{
								default:{img:resources.get("brick1.gif"),x:0,renderW:0.6*DEFLENGTH,renderH:0.6*DEFLENGTH}
							},
							position:[{x:200,y:CHEIGHT-4*DEFLENGTH,width:5,height:1},{x:1250,y:180},{x:1450,y:150,width:7,height:1}]
						}]
						var livingsData = [{
							imgs:{
								default:{img:resources.get("monster.png"),x:0,spiritW:60,renderW:0.8*DEFLENGTH,renderH:0.8*DEFLENGTH},
								die:{img:resources.get("monsterD.gif"),x:0,spiritW:60,renderW:0.8*DEFLENGTH,renderH:0.8*DEFLENGTH,crushH:0.1*DEFLENGTH}
							},
							position:[{x:200,y:290},{x:300,y:290},{x:650,y:290},{x:1110,y:160},{x:1550,y:110}]
						}]
						var models = [];
						var livings = [];
						//?????????????????????
						modelsData.forEach(function(modelsD,index){
							modelsD.position.forEach(function(position,index){
								var ii=position.width|| 1,
									jj=position.height||1;
								for(var i=0;i<ii;i++){
									for(var j=0;j<jj;j++){
										var model = new Model(modelsD.imgs,{x:position.x+i*modelsD.imgs.default.renderW , y:position.y+j*modelsD.imgs.default.renderH})
										models.push(model)
									}
								}
							})
						})
						//???????????????
						livingsData.forEach(function(livingsD,index){
							livingsD.position.forEach(function(position,index){
								var living = new Livings(livingsD.imgs,{x:position.x,y:position.y})
								livings.push(living)
							})
						})
						//??????????????????
						var player = new Player({
							moveR:{img:resources.get("marioR.png"),x:0,spiritW:60,renderW:DEFLENGTH,renderH:DEFLENGTH,crushW:0.8*DEFLENGTH},
							moveL:{img:resources.get("marioL.png"),x:0,spiritW:60,renderW:DEFLENGTH,renderH:DEFLENGTH,crushW:0.7*DEFLENGTH},
							jumpR:{img:resources.get("marioJR.gif"),x:0,renderW:DEFLENGTH,renderH:DEFLENGTH},
							jumpL:{img:resources.get("marioJL.gif"),x:0,renderW:DEFLENGTH,renderH:DEFLENGTH}
						},{x:0,y:220},{onCrush:onCrush})
						function onCrush(model){
							if(model.constructor==Livings&&model.crush.top){
								model.die();
								var index = livings.indexOf(model);
								livings.splice(index,1)
							}else if(model.constructor==Livings){
								this.die();

							}
						}
						window.map=map;

						//livings.push(player);
						var loop = new GameLoop(function(interTime){
							var frameTime = 30;//?????????????????????
							var renderModels = getRenderModels(models,map),
								renderLivings = getRenderModels(livings,map);
							renderLivings.forEach(function(living,item){
								living.autoMove(frameTime);
							})
							renderLivings.push(player)
							Model.prototype.alls = Array.prototype.concat(renderLivings,renderModels);
							player.update(control,canvas,frameTime,loop);
							map.update(control,player,canvas,frameTime);
							camera.drawBackground(map);
							camera.drawModels(renderModels,map);
							camera.drawLivings(renderLivings,map);
							camera.drawGameInfos(loop.info);
						},30)//30fps
						loop.frame()

					})
				};
				animation();
			}
		});

		this.addEventListener('animationend', (e) => {
			this.style.removeProperty('animation-name');
		});
	}

	addCoin() {
		const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
		const id = 'coin';
		const use = document.createElementNS('http://www.w3.org/2000/svg', 'use');

		use.setAttributeNS('http://www.w3.org/1999/xlink','href', '#' + id);

		svg.addEventListener('animationend', (e) => {
			svg.remove();
		});
		svg.appendChild(use);
		svg.classList.add('multicoin-block__coin');

		this.appendChild(svg);
	}
}

customElements.define('multicoin-block', MulticoinBlock);