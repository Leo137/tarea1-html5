<!doctype html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8" />
    <title>Phaser - Making your first game, part 1</title>
    <script type="text/javascript" src="phaser-master/build/phaser.js"></script>
    <style type="text/css">
        body {
            margin: 0;
        }
    </style>
</head>
<body>

<script type="text/javascript">
var game = new Phaser.Game(640, 480, Phaser.AUTO);
var cursors;
var player;
var map;
var layer;
var scroll; 
var shootButton;
var cooldown = 0;
var bullets;
var bullet;
var first = true;
var arrows;
var label1;
var label2;

var keys;
var doors;

var key_counter = 0;

var enemys_1;
var enemys_2;

var background;
var keyboard;
var keyboard_z;
var fx;


function shootBullet(){
    console.log("pium pium");
    fx.play('shot',0,0.3);
    //  Grab the first bullet we can from the pool
    bullet = bullets.getFirstExists(false);
    if (bullet)
    {
        var facing = -1;

        if(player.scale.x < 0){
            facing = 1;
        }

        bullet.reset(player.x+8*facing, player.y + 10);
        bullet.body.velocity.x = 400*facing;
        bullet.body.setSize(8, 8, 0, -4);
        //  And fire it
        
    }
}


function showUpArrows(){

    var arrow;
    arrow = arrows.getFirstExists(false);

    arrow.reset();
    arrow.cameraOffset.setTo(320/2,440);

    arrow = arrows.getFirstExists(false);

    arrow.reset();
    arrow.cameraOffset.setTo(320,440);

    arrow = arrows.getFirstExists(false);

    arrow.reset();
    arrow.cameraOffset.setTo(320 + 320/2,440);

    game.time.events.repeat(Phaser.Timer.SECOND * 1.0, 6, flickArrows, this);
    game.time.events.add(Phaser.Timer.SECOND * 7.0, killArrows, this);
}

function flickArrows(){
    arrows.forEachExists(function (item){
        item.alpha = 1.0;
        game.world.bringToTop(arrows);
    }, this);
    game.time.events.add(Phaser.Timer.SECOND * 0.3, flickArrows2, this);
}

function flickArrows2(){
    arrows.forEachExists(function (item){
        item.alpha = 0.0;
    }, this);
}

function killArrows(){
    arrows.forEachExists(function (item){
        item.kill();
    }, this);
}


function setActive(){
    this.active = true;
   
}


function enemyTouchPlayer(){
    if(player.alive)fx.play('alien death');
    player.kill();
    
}

function bulletTouchEnemy1(bullet, enemy){
    bullet.kill();
    fx.play('squit');
    //enemy.kill();
}

function bulletTouchEnemy(bullet, enemy){
    bullet.kill();
    fx.play('squit');
    enemy.kill();
}

function bulletTouchLayer(bullet, layer){
    bullet.kill();
}

function keysTouchPlayer(player, key){
    key.kill();
    fx.play('ping');
    key_counter ++;
}

function doorsTouchPlayer(player, door){

    if(key_counter > 0){
        door.kill();
        key_counter --;
        fx.play('squit');
    }
    else{

        game.physics.arcade.collide(player,door);
    }
    
}

function render() {

    
}

function restartGame(){
    game.state.restart();
}


BasicGame = {};

BasicGame.Game = function(){ }; 
 
BasicGame.Game.prototype = { 

preload: function() {
    game.load.image('background', 'assets/sprites/bg.png');

    game.load.image('map', 'assets/tiles/tilemap.png');
    game.load.tilemap('tilemap', 'assets/maps/tilemap.json', null, Phaser.Tilemap.TILED_JSON);
    
    game.load.spritesheet('player', 'assets/sprites/player.png', 64, 64);
    game.load.image('bullet', 'assets/sprites/bullet.png');

    game.load.image('arrow', 'assets/sprites/arrow.png');
    game.load.image('enemy1', 'assets/sprites/enemy1.png');

    game.load.spritesheet('enemy2', 'assets/sprites/enemy2.png', 32, 32);

    game.load.image('key', 'assets/sprites/key.png');
    game.load.image('door', 'assets/sprites/door.png');

    game.load.image('keyboard', 'assets/sprites/keyboard.png');
    game.load.image('keyboard_z', 'assets/sprites/keyboard_z.png');

    game.load.audio('sfx', 'assets/sfx/fx_mixdown.ogg');

    cursors = game.input.keyboard.createCursorKeys();

    shootButton = game.input.keyboard.addKey(Phaser.Keyboard.Z);

    game.time.advancedTiming = true;
},

create: function() {

    game.stage.backgroundColor = '#787878';

    fx = game.add.audio('sfx');

    //  And this defines the markers.

    //  They consist of a key (for replaying), the time the sound starts and the duration, both given in seconds.
    //  You can also set the volume and loop state, although we don't use them in this example (see the docs)

    fx.addMarker('alien death', 1, 1.0);
    fx.addMarker('boss hit', 3, 0.5);
    fx.addMarker('escape', 4, 3.2);
    fx.addMarker('meow', 8, 0.5);
    fx.addMarker('numkey', 9, 0.1);
    fx.addMarker('ping', 10, 1.0);
    fx.addMarker('death', 12, 4.2);
    fx.addMarker('shot', 17, 1.0);
    fx.addMarker('squit', 19, 0.3);

    background = game.add.sprite(300, 0, 'background');
    background.anchor.setTo(0.5,1.0);
    background.scale.setTo(2.0,2.0);
    background.fixedToCamera = true;

    keyboard = game.add.sprite(550, 4500, 'keyboard');
    keyboard.anchor.setTo(0.5,0.5);
    keyboard.scale.setTo(1.0,1.0);
    keyboard.blendMode = PIXI.blendModes.SCREEN;
    keyboard.alpha = 0.5;

    keyboard_z = game.add.sprite(75, 4000, 'keyboard_z');
    keyboard_z.anchor.setTo(0.5,0.5);
    keyboard_z.scale.setTo(1.0,1.0);
    keyboard_z.blendMode = PIXI.blendModes.SCREEN;
    keyboard_z.alpha = 0.5;

    map = game.add.tilemap('tilemap');
    map.addTilesetImage('map');
    map.setCollisionBetween(0,100);
    layer = map.createLayer('Capa de Patrones 1');
    
    layer.resizeWorld();
    layer.wrap = true;
    

    player = game.add.sprite(30, 4600, 'player');
    first = true;
    player.animations.add('walk',[0,1,0,2]);
    player.animations.play('walk', 10, true);
    player.anchor.setTo(0.5,0.5);
    player.scale.setTo(0.5,0.5);

    //monito.animations.stop('walk',true);

    game.physics.enable(player, Phaser.Physics.ARCADE);
    player.body.collideWorldBounds = true;
    player.body.outOfBoundsKill = true;
    player.body.setSize(32,50,0,3);

    player.scale.x = -0.5;

    //  Our bullet group
    bullets = game.add.group();
    bullets.enableBody = true;
    bullets.physicsBodyType = Phaser.Physics.ARCADE;
    bullets.createMultiple(30, 'bullet');
    bullets.setAll('anchor.x', 0.5);
    bullets.setAll('anchor.y', 1);
    bullets.setAll('outOfBoundsKill', true);
    bullets.setAll('checkWorldBounds', true);


    //  Our arrow group
    arrows = game.add.group();
    arrows.createMultiple(3, 'arrow');
    arrows.setAll('anchor.x', 0.5);
    arrows.setAll('anchor.y', 1);
    arrows.setAll('fixedToCamera', true);
    arrows.setAll('alpha', 0.0);

    //  Our enemy group
    enemys_1 = game.add.group();
    enemys_1.enableBody = true;
    enemys_1.physicsBodyType = Phaser.Physics.ARCADE;
    enemys_1.createMultiple(300, 'enemy1');
    enemys_1.setAll('anchor.x', 0.5);
    enemys_1.setAll('anchor.y', 0.5);
    //enemys_1.setAll('outOfBoundsKill', true);
    //enemys_1.setAll('checkWorldBounds', true);

    //  Our enemy group
    enemys_2 = game.add.group();
    enemys_2.enableBody = true;
    enemys_2.physicsBodyType = Phaser.Physics.ARCADE;
    enemys_2.createMultiple(300, 'enemy2');
    enemys_2.setAll('anchor.x', 0.5);
    enemys_2.setAll('anchor.y', 0.5);
    //enemys_2.setAll('outOfBoundsKill', true);
    //enemys_2.setAll('checkWorldBounds', true);


    //  Our key group
    keys = game.add.group();
    keys.enableBody = true;
    keys.physicsBodyType = Phaser.Physics.ARCADE;
    keys.createMultiple(300, 'key');
    keys.setAll('anchor.x', 0.5);
    keys.setAll('anchor.y', 0.5);
    //keys.setAll('outOfBoundsKill', true);
    //keys.setAll('checkWorldBounds', true);

    //  Our doors group
    doors = game.add.group();
    doors.enableBody = true;
    doors.physicsBodyType = Phaser.Physics.ARCADE;
    doors.createMultiple(300, 'door');
    doors.setAll('anchor.x', 0.5);
    doors.setAll('anchor.y', 0.5);
    //doors.setAll('outOfBoundsKill', true);
    //doors.setAll('checkWorldBounds', true);




    game.camera.y = 4600;
    scroll = Math.floor(game.camera.y);

    // transformar enemigos a instancias

    for(var y = 0; y < map.height; ++y){
        for(var x = 0; x < map.width; ++x){
            var tile = map.layers[0].data[y][x];
            map.tile
            if(tile != null){
                if(tile.index == 16){
                    var enemy1 = enemys_1.getFirstExists(false);
                    enemy1.reset(tile.worldX,tile.worldY);
                    enemy1.body.setSize(16, 16, 2, 8);
                    map.removeTile(x,y);
                }

                if(tile.index == 17){
                    var enemy2 = enemys_2.getFirstExists(false);
                    enemy2.reset(tile.worldX+8,tile.worldY+6);
                    enemy2.body.setSize(14, 19, 0, 6);
                    enemy2.animations.add('walk',[0,1,0,2]);
                    enemy2.animations.play('walk', 10, true);
                    enemy2.timeto = 0;
                    enemy2.active = false;
                    if(player.position.x < enemy2.position.x ){
                        enemy2.scale.x = -1.0;
                    }
                    else{
                        enemy2.scale.x = +1.0;
                    }
                    map.removeTile(x,y);
                }

                if(tile.index == 27){
                    var key = keys.getFirstExists(false);
                    key.reset(tile.worldX,tile.worldY);
                    key.body.setSize(16, 26, 1, 0);
                    map.removeTile(x,y);
                }

                if(tile.index == 28){
                    var door = doors.getFirstExists(false);
                    door.reset(tile.worldX,tile.worldY);
                    door.body.setSize(64, 64, 1, 0);
        
                    door.body.immovable = true;
                    map.removeTile(x,y);
                }

            }
        }
    }

    //  This is the BitmapData we're going to be drawing to

    label1 = game.add.text(550, 20, "Metros: 0.00", { font: "bold 16px Arial", fill: "#FFFFFF" });
    label1.fixedToCamera = true;
    label1.anchor.set(0.5);
    label1.stroke =  'black';
    label1.strokeThickness=2;

    label2 = game.add.text(538, 40, "Llaves: 0", { font: "bold 16px Arial", fill: "#FFFFFF" });
    label2.fixedToCamera = true;
    label2.anchor.set(0.5);
    label2.stroke =  'black';
    label2.strokeThickness=2;

    showUpArrows();

},



update: function() {
    var border = 10;
    var speed1 = 100;
    var speed2 = 60;

    if(!player.alive){
        if(!this.restart)game.time.events.add(Phaser.Timer.SECOND * 5.0, restartGame, this);
        this.restart = true;
    }
    enemys_1.forEachExists(function(item){
    
        item.body.acceleration.y = 1500;
        if(item.scale.x < 0){
            item.body.velocity.x = speed1;
            item.body.position.x +=border;
            game.physics.arcade.collide(item, layer);
         
            if(!item.body.onFloor()){
                item.scale.x = 1;
                //item.scale.y = item.scale.y*-1;
            }
            item.body.position.x -=border;
        }
        else{
            item.body.velocity.x = -speed1;

            item.body.position.x -=border;
            game.physics.arcade.collide(item, layer);
            if(!item.body.onFloor()){
                item.scale.x = -1;
                //item.scale.y = item.scale.y*-1;
            }
            item.body.position.x +=border;
        }

        game.physics.arcade.collide(item, layer);
        if(item.body.onWall()){
            item.scale.x = item.scale.x * -1;
        }        
    },this);


    enemys_2.forEachExists(function(item){
        item.body.acceleration.y = 1500;
        game.physics.arcade.collide(item, layer);
        if(!item.active){
            item.animations.stop('walk',true);
            if(!item.inCamera)return;
            else{
                if(!item.added){
                    game.time.events.add(Phaser.Timer.SECOND * 1.5, setActive, item);
                    item.added = true;
                }
            }
            return;
        }

        if(!player){
            item.animations.stop('walk',true);
            item.body.velocity.x = 0;
            return;
        }  

        if(item.position.y < player.position.y - 80){
            item.animations.stop('walk',true);
            item.body.velocity.x = 0;
            return;
        }

        if(item.position.x < player.position.x - 90 || item.position.x > player.position.x + 90){
            item.animations.stop('walk',true);
            item.body.velocity.x = 0;
            return;
        }
        item.animations.play('walk', 10, true);

        item.body.velocity.y += 1;

        if(item.scale.x > 0){
            item.body.velocity.x = speed2;

        }
        else{
            item.body.velocity.x = -speed2;

        }

        

        if(player.position.x < item.position.x ){
            if(item.timeto > 0 ){
                item.timeto --;
            }
            else{
            item.timeto = 20;
            item.scale.x = -1.0;
            }
        }
        else{
            if(item.timeto > 0 ){
                item.timeto --;
            }
            else{
            item.timeto = 20;
            item.scale.x = +1.0;
            }
        }          
    },this);


    game.physics.arcade.collide(player, layer);
    if (cursors.left.isDown)
    {
        player.animations.play('walk', 10, true);
        player.scale.x = 0.5;
        player.body.velocity.x = -200;
    }
    else if (cursors.right.isDown)
    {
        player.animations.play('walk', 10, true);
        player.scale.x = -0.5;
        player.body.velocity.x = 200;
    }
    else{
        player.animations.stop('walk',true);
        player.body.velocity.x = 0;
    }

    if(cursors.up.isDown & player.body.onFloor()){
        player.body.velocity.y = -400;
        fx.play('numkey');
    }

    if(player.body.onBeginContact){
        player.body.velocity.y = 0;
    }

    if(shootButton.isDown && cooldown<=0){
        cooldown = 300.0;
        shootBullet();
    }

    if(!player.inCamera && player.position.y > game.camera.y){
        if(first){
            first = false;
        }
        else{
            if(player.alive) fx.play('death');
        player.kill();
        
        //console.log("cago");
        }
    }

    if(keyboard.inCamera){
        keyboard.rotation = Math.sin(scroll*60)*0.2;
    }

    if(keyboard_z.inCamera){
        keyboard_z.rotation = Math.sin(scroll*60)*0.2;
    }
    
    cooldown = cooldown > 0 ? cooldown - 19 : 0;
    scroll -= 0.21;
    if(player.alive){
        game.camera.y = Math.floor(scroll);
        background.cameraOffset.y = 1000
        +Math.floor(scroll/3)/2;
    }
    player.body.acceleration.y = 1500;

    game.physics.arcade.overlap(enemys_1, player, enemyTouchPlayer, null, this);
    game.physics.arcade.overlap(enemys_2, player, enemyTouchPlayer, null, this);

    game.physics.arcade.overlap(bullets, enemys_1, bulletTouchEnemy1, null, this);
    game.physics.arcade.overlap(bullets, enemys_2, bulletTouchEnemy, null, this);

    game.physics.arcade.overlap(bullets, layer, bulletTouchLayer, null, this);

    game.physics.arcade.overlap(keys, player, keysTouchPlayer, null, this);
    game.physics.arcade.overlap(doors, player, doorsTouchPlayer, null, this);

    enemys_2.forEachExists(function(item){
        //game.debug.body(item);
        //game.debug.bodyInfo(item, 32, 32);
    },this);

    game.debug.text(game.time.fps || '--', 2, 14, "#00ff00");
    var meters = (4320-game.camera.y)/40;
    label1.text = "Metros: "+meters.toFixed(2);
    label2.text = "Llaves: "+key_counter.toFixed(0);

}

}

game.state.add('Game',BasicGame.Game);
game.state.start('Game');

</script>

</body>
</html>     