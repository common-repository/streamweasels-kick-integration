function streamWeaselsKick(opts) {
    this._opts = opts;
    this.uuid = opts.uuid;
    this.channels = opts.channels;
    this.channelsLength = opts.channelsLength;
    this.limit = opts.limit;
    this.layout = opts.layout;
    this.embed = opts.embed;
    this.embedTitle = opts.embedTitle;
    this.embedTitlePosition = opts.embedTitlePosition;
    this.embedMuted = opts.embedMuted;
    this.showOffline = opts.showOffline;
    this.showOfflineText = opts.showOfflineText;
    this.showOfflineImage = opts.showOfflineImage;
    this.autoplay = opts.autoplay;
    this.autoplaySelect = opts.autoplaySelect;
    this.featured = opts.featured;
    this.offlineImage = opts.offlineImage;
    this.logoImage = opts.logoImage;
    this.profileImage = opts.profileImage;
    this.tileLayout = opts.tileLayout;
    this.tileSorting = opts.tileSorting;
    this.tileLive = opts.tileLive;
    this.logoBgColour = opts.logoBgColour;
    this.logoBorderColour = opts.logoBorderColour;
    this.maxWidth = opts.maxWidth;
    this.tileBgColour = opts.tileBgColour;
    this.tileTitleColour = opts.tileTitleColour;
    this.tileSubtitleColour = opts.tileSubtitleColour;
    this.tileRoundedCorners = opts.tileRoundedCorners;
    this.hoverColour = opts.hoverColour;
    this.wrapper = document.querySelector('.cp-streamweasels-kick--'+this.uuid);
    this.target = this.wrapper.querySelector('.cp-streamweasels-kick__streams');
    this.loading = this.wrapper.querySelector('.cp-streamweasels-kick__loader');
    this.player = this.wrapper.querySelector('.cp-streamweasels-kick__player');
    if (this.layout == 'vods') {
        this._getVods();
    } else {
        this._getStreams(this.channels);
    }
}

streamWeaselsKick.prototype = Object.create(null,{
    constructor: {
        value: streamWeaselsKick
    }, 
    _getStreams:{
        value: function(channels){
            var streamCount = 0;
            var channelsArr = channels.split(',');
            var xhr = [];
            this.wrapper.dataset.channelCount = this.channelsLength;
            for (var $i = 0; $i < channelsArr.length; $i++) {
                (function($i){
                    var channelID = channelsArr[$i];
                    xhr[$i] = new XMLHttpRequest();
                    xhr[$i].open("GET", "https://kick.com/api/v1/channels/"+channelID);
                    // xhr[$i].open("GET", "https://kick.com/api/v1/categories/2");
                    console.log("https://kick.com/api/v1/channels/"+channelID);
                    xhr[$i].onreadystatechange = function () {
                        if (xhr[$i].readyState === 4) {
                            var status = xhr[$i].status;
                            if (status === 200) {
                                var data = JSON.parse(xhr[$i].responseText)
                                console.log(data)
                                if (data && streamCount < this.limit) {
                                    this._appendStream(data);
                                    streamCount++;
                                }
                            } else {
                                this.wrapper.dataset.channelCount--;
                                console.log('error finding data: ' + channelID + ': '  + status);
                            }
                        }                                      
                    }.bind(this);
                    xhr[$i].send();
                }.bind(this))($i);
            }
        }
    }, 	      
    _getVods:{
        value: function(){
                var vodCount = 0;
                var xhrVods = [];
                var randomVodVal = Math.floor(1000 + Math.random() * 9000);
                xhrVods[randomVodVal] = new XMLHttpRequest();
                xhrVods[randomVodVal].open("GET", "https://kick.com/api/v2/channels/"+this.wrapper.dataset.vodsChannel+"/clips?cursor=0&sort=date&time="+this.wrapper.dataset.clipPeriod);
                xhrVods[randomVodVal].onreadystatechange = function () {
                    if (xhrVods[randomVodVal].readyState === 4) {
                        var data = JSON.parse(xhrVods[randomVodVal].responseText)
                        var vods = data.clips;
                        if (vods.length) {
                            this.wrapper.dataset.channelCount = vods.length;
                            for (var $i = 0; $i < vods.length; $i++) {
                                vodCount++;
                                var vod = data.clips[$i];                           
                                if (vodCount > this.limit) {
                                    console.log('No more vods - limit of '+this.limit+' reached.')                          
                                    this._clickHandler();
                                    break;
                                }
                                var language = vod.language;
                                var title = vod.title;
                                var channelTitle = vod.channel.slug;
                                var date = this._daysAgo(vod.created_at);
                                var profilePic = vod.channel.profile_picture || '';
                                var thumbnail = vod.thumbnail_url || '';
                                var viewers = vod.view_count;
                                var ID = vod.id;
                                var duration = vod.duration;
                                var type = 'clips';
                                var url = 'https://kick.com/'+channelTitle+'?clip='+ID;
                                var liveInfo = `
                                ${!this.profileImage ? `<img class="cp-stream__logo" src="${profilePic}" style="${this.logoBorderColour && 'border-color:'+this.logoBorderColour};${this.logoBgColour && 'background-color:'+this.logoBgColour}">` : ''}
                                <div class="cp-stream__info-wrapper">
                                    <span class="cp-stream__title" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${channelTitle}</span>
                                    <span class="cp-stream__meta" style="${this.tileSubtitleColour && 'color:'+this.tileSubtitleColour }><strong class="cp-stream__meta--game">${title}</strong></span>
                                </div>
                                `                                
                                var html = `
                                <div class="cp-stream cp-stream--vod" data-user="${channelTitle}" style="${this.hoverColour && 'background-color:'+this.hoverColour}">
                                    <a class="cp-stream__inner" href="${url}" target="_blank" title="${title}" data-channel-name="${channelTitle}" data-viewers="${viewers}" data-date="${vod.created_at}" data-type="${type}" data-id="${ID}">
                                        <div class="cp-stream__image">
                                            <img loading="lazy" src="${thumbnail}">
                                            <div class="cp-stream__overlay"></div>
                                            <div class="cp-stream__status cp-stream__status-viewers"><span>${viewers} views</span></div>
                                            <div class="cp-stream__status cp-stream__status-duration"><span>0:${duration}</span></div>
                                            <div class="cp-stream__status cp-stream__status-date"><span>${date}</span></div>
                                        </div>
                                        <div class="cp-stream__info">
                                        ${liveInfo}
                                    </div>
                                    </a>
                                </div>
                                `;
                                this.target.insertAdjacentHTML('beforeend', html);
                                this.wrapper.dataset.online++;
                                var streamTotal = parseInt(this.wrapper.dataset.online);
                                if (this.wrapper.dataset.channelCount == streamTotal || streamTotal == this.limit) {
                                    this._postAppend();
                                }
                            }
                        } else {
                            this.wrapper.classList.add('cp-streamweasels--no-vods')
                            if (this.showOfflineText || this.showOfflineImage) {
                                this._offlineMessage();       
                            }
                        }
                    }
                }.bind(this);

            xhrVods[randomVodVal].send();
            if (this.loading) {
                this.loading.remove();
            }                  
        }
    },        
    _appendStream:{
        value: function(stream) {
            var user = stream;
			if (stream.livestream) {
                var status = 'online';
                var thumbnail = user.livestream.thumbnail.url || '';
                var profilePic = user.user.profile_pic || '';
                var title = user.livestream.session_title;
                var channelTitle = user.slug;
                var slug = user.slug;
                var language = 'en';
                var type = 'video';
                var viewers = user.livestream.viewers;
                this.wrapper.dataset.online++;
                if (this.tileLive == 'live') {
                    liveStatus = `<div class="cp-stream__status cp-stream__status-live"><span>LIVE</span></div>`
                } else if (this.tileLive == 'viewer') {
                    liveStatus = `<div class="cp-stream__status cp-stream__status-viewers"><span>${this._roundViewers(viewers)} <span>viewers</span></span></div>`
                } else if (this.tileLive == 'online') {
                    liveStatus = `<div class="cp-stream__status cp-stream__status-online"><span class="cp-stream__online-dot"></span><span>${this._roundViewers(viewers)}</span></div>`
                } else if (this.tileLive == 'none') {
                    liveStatus = `<div class="cp-stream__status cp-stream__status-none"></div>`
                } else {
                    liveStatus = `<div class="cp-stream__status cp-stream__status-viewers"><span>${this._roundViewers(viewers)} <span>viewers</span></span></div>`
                }  
            } else {
                var status = 'offline';
                var thumbnail = this.offlineImage ||  user.banner_image?.url || streamWeaselsKickVars.kickOffline;
                var profilePic = this.logoImage || user.user.profile_pic || streamWeaselsKickVars.kickIcon;
                var title = 'Offline';
                var channelTitle = user.slug;
                var slug = user.slug;
                var language = 'en';
                var type = 'video';
                var viewers = '0';
                var liveStatus = '';
                this.wrapper.dataset.offline++;
            }
            var liveInfo = `
            ${!this.profileImage ? `<img class="cp-stream__logo" src="${profilePic}" style="${this.logoBorderColour && 'border-color:'+this.logoBorderColour};${this.logoBgColour && 'background-color:'+this.logoBgColour}">` : ''}
            <div class="cp-stream__info-wrapper">
                <span class="cp-stream__title" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${channelTitle}</span>
                <span class="cp-stream__meta" style="${this.tileSubtitleColour && 'color:'+this.tileSubtitleColour }"><strong class="cp-stream__meta--game">${title}</strong></span>
            </div>
            `    
            if (this.wrapper.dataset.enableClassic == 1 && status == 'online') {
                var liveInfo = `
                <div class="cp-stream__info-wrapper">
                    <span class="cp-stream__title cp-stream__title--classic" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }"><span class="swti-live-marker"></span>${this.wrapper.dataset.classicOnlineText}</span>
                </div>
                `
            }
            if (this.wrapper.dataset.enableClassic == 1 && status == 'offline') {
                var liveInfo = `
                <div class="cp-stream__info-wrapper">
                    <span class="cp-stream__title cp-stream__title--classic" style="${this.tileTitleColour && 'color:'+this.tileTitleColour }">${this.wrapper.dataset.classicOfflineText}</span>
                </div>
                `
            }                                   
            var html = `
            <div class="cp-stream cp-stream--${status} cp-stream--classic-${this.wrapper.dataset.enableClassic}" data-user="${channelTitle}" style="${this.hoverColour && 'background-color:'+this.hoverColour}">
                <a class="cp-stream__inner" href="https://www.kick.com/${slug}" target="_blank" title="${title}" data-channel-name="${channelTitle}" data-viewers="${viewers}" data-language="${language}" data-type="${type}" data-status="${status}">
                    <div class="cp-stream__image">
                        <img loading="lazy" src="${thumbnail}">
                        <div class="cp-stream__overlay"></div>
                        <div class="cp-stream__embed-inside"></div>
                    </div>
                    <div class="cp-stream__info">
                    ${liveInfo}
                </div>
                ${liveStatus}
                </a>
            </div>
            `;
            if (stream.livestream || this.showOffline) {
                this.target.insertAdjacentHTML('beforeend', html);
            }
            var streamTotal = parseInt(this.wrapper.dataset.online) + parseInt(this.wrapper.dataset.offline);
            if (this.wrapper.dataset.channelCount == streamTotal || streamTotal == this.limit) {
                this._postAppend();
            }
		}
    },  
    _postAppend:{
        value: function() {
            if (this.loading) {
                this.loading.remove();
            }
            if (this.wrapper.dataset.online == 0) {
                if (this.showOfflineText || this.showOfflineImage) {
                    this._offlineMessage();       
                }
            }
            if (this.layout == 'feature') {
                if (this.target.children.length) {
                var nodeCount = this.target.querySelectorAll('.cp-stream');
                    if(nodeCount.length == 1) {
                        var node = nodeCount[0];
                        var clone = node.cloneNode(true);
                        this.target.appendChild(clone)
                    }
                setTimeout(function() {
                    this._startFlipster(this.wrapper,this.target)
                }.bind(this), 300)
                }
            }
            if (this.layout == 'status') {
                this.wrapper.classList.add('cp-streamweasels-kick--loaded')
                setTimeout(function() {
                    this.target.classList.add('cp-streamweasels-kick__streams--loaded')
                }.bind(this), 1000)
                setTimeout(function() {
                    this.wrapper.classList.add('cp-streamweasels-kick--animation-finished')
                }.bind(this), 2000)  
                if (this.target.classList.contains('cp-streamweasels-kick__streams--carousel-0')) {
                    setTimeout(function() {
                        jQuery(this.target).slick({
                            dots: false,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            swipeToSlide: true,
                            prevArrow: '<button type="button" class="slick-prev"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/><path d="M0-.5h24v24H0z" fill="none"/></svg></button>',
                            nextArrow: '<button type="button" class="slick-next"><svg fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24"><path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/><path d="M0-.25h24v24H0z" fill="none"/></svg></button>',
                        })
                    }.bind(this), 3000)
                }
            }                                   
            this._sortStreams();        
            this._clickHandler();   
        }
    },    
    _sortStreams:{
        value: function() {
            var streams = this.wrapper.querySelector('.cp-streamweasels-kick__streams');
            [...streams.children]
                .sort(function(a,b) {      
                    if (this.tileSorting == 'alpha') {
                        return (a.children[0].dataset.channelName>b.children[0].dataset.channelName? 1: -1);
                    }                      
                    if (this.tileSorting == 'least') {
                        return a.children[0].dataset.viewers - b.children[0].dataset.viewers;
                    }                                
                    if (this.tileSorting == 'most') {
                        return b.children[0].dataset.viewers - a.children[0].dataset.viewers;
                    }
                    if (this.tileSorting == 'latest') {
                        return b.children[0].dataset.date - a.children[0].dataset.date;
                    }                    
                    if (this.tileSorting == 'random') {
                        return 0.5 - Math.random()
                    }                                                       
                }.bind(this))
                .forEach(node=> {
                    streams.appendChild(node)
                });
        }
    },   
    _clickHandler:{
        value: function() {
            if (this.autoplay && this.embed !== 'kick') {
                var streams = this.wrapper.querySelectorAll('.cp-stream--online')
                if (streams.length > 0) {
                    var streamCount = streams.length - 1;
                    var streamRandom = Math.floor(Math.random() * streams.length)
                    if (this.autoplaySelect == 'most') {
                        var featuredStream = streams[0].querySelector('a')
                    } else if (this.autoplaySelect == 'least') {
                        var featuredStream = streams[streamCount].querySelector('a')
                    } else if (this.autoplaySelect == 'random') {
                        var featuredStream = streams[streamRandom].querySelector('a')
                    } else {
                        var featuredStream = streams[0].querySelector('a')
                    }
                    if (this.featured) {
                        streams.forEach(tile => {
                            if (this.featured.toLowerCase() == tile.children[0].dataset.channelName.toLowerCase()) {
                                featuredStream = tile.children[0];
                            }
                        })
                    }
                    if (this.layout == 'vods') {
                        this._embedVod(featuredStream);                         
                    } else {
                        this._embedStream(featuredStream);                         
                    }
                }
            }               
            var tiles = this.wrapper.querySelectorAll('.cp-stream__inner');
            tiles.forEach(tile => {
                tile.addEventListener('click', function(e) {
                    e.preventDefault();                       
                    if (this.layout == 'vods') {
                        this._embedVod(tile);                         
                    } else {
                        this._embedStream(tile);                         
                    }
                }.bind(this));
            })
        }
    },
    _embedStream:{
        value: function(channelNode) {
            var body = document.querySelector('body')
            var muted = this.embedMuted ? '?muted=true' : '?muted=false'
            var modalHtml =
            `<div class="cp-streamweasels-kick-modal">
                ${(this.embedTitle && this.embedTitlePosition == 'top' && channelNode.dataset.status == 'online') ? '<div class="cp-streamweasels-kick-modal__title cp-streamweasels-kick-modal__title--'+this.embedChat+'"><p>'+channelNode.getAttribute('title')+'</p></div>' : ''}
                <div class="cp-streamweasels-kick-modal__player cp-streamweasels-kick-modal__player--${this.embedChat}"></div>
                ${(this.embedTitle && this.embedTitlePosition == 'bottom' && channelNode.dataset.status == 'online') ? '<div class="cp-streamweasels-kick-modal__title cp-streamweasels-kick-modal__title--'+this.embedChat+'"><p>'+channelNode.getAttribute('title')+'</p></div>' : ''}
            </div>`
            var player = this.wrapper.querySelector('.cp-streamweasels-kick__player');    
            if (this.layout == 'status' && this.embed == 'page') {
                this.embed = 'popup'
            }            
            if (this.embed == 'kick') {
                window.open('https://kick.com/'+channelNode.dataset.channelName, '_blank');
            }
            if (this.embed == 'page') {
                player.innerHTML = '';
                var iframe = document.createElement("iframe");
                iframe.src = "https://player.kick.com/"+channelNode.dataset.channelName+muted;
                iframe.width = "100%";
                iframe.height = "100%";
                iframe.scrolling = "no";
                iframe.allowFullscreen = "true";
                player.appendChild(iframe);

                if (this.layout == 'wall') {
                    player.scrollIntoView();
                }
                this.wrapper.classList.add('cp-streamweasels-kick--embed-page-active');
                this.player.classList.add('cp-streamweasels-kick__player--embed-page-active');
            }
            if (this.embed == 'popup') {
                body.insertAdjacentHTML('beforeend', modalHtml);
                var wrapper = document.querySelector('.cp-streamweasels-kick-modal')
                var modalPlayer = document.querySelector('.cp-streamweasels-kick-modal__player')
                var iframe = document.createElement("iframe");
                iframe.src = "https://player.kick.com/"+channelNode.dataset.channelName+muted;
                iframe.width = "100%";
                iframe.height = "100%";
                iframe.scrolling = "no";
                iframe.allowFullscreen = "true";
                iframe.id = "kick-embed";
                modalPlayer.appendChild(iframe);
                this._modalControls(wrapper);     
                this.wrapper.classList.add('cp-streamweasels-kick--embed-popup-active');    
            }
            if (this.layout == 'feature' && this.embed == 'inside') {
                setTimeout(function() {
                    var featureEmbed = this.wrapper.querySelector('.flipster__item--current');
                    var featureEmbedInner = featureEmbed.querySelector('.cp-stream__image');
                    var featureEmbedIframe = featureEmbedInner.querySelector('iframe')
                    var featureEmbedImage = featureEmbedInner.querySelector('img');
                    var featureEmbedInnerWidth = featureEmbedImage.width
                    var featureEmbedInnerHeight = featureEmbedImage.height
                    if (this.tileLayout == 'detailed') {
                        featureEmbedInnerHeight = featureEmbedInnerHeight + 48;
                    }
                    featureEmbed.classList.add('flipster__item--embed')
                    if (featureEmbedIframe) {
                        featureEmbedIframe.remove()
                    }
                    var iframe = document.createElement("iframe");
                    iframe.src = "https://player.kick.com/"+channelNode.dataset.channelName+muted;
                    iframe.width = featureEmbedInnerWidth+'px',
                    iframe.height = featureEmbedInnerHeight+'px',
                    iframe.scrolling = "no";
                    iframe.id = "kick-embed";
                    featureEmbedInner.appendChild(iframe);                    
                    this.wrapper.classList.add('cp-streamweasels-kick--embed-page-active');
                    this.embedTitle = false;
                }.bind(this), 300)
            }             
            if (this.embedTitle && this.embed == 'page' && channelNode.dataset.status == 'online') {
                this._embedTitle(channelNode.getAttribute('title'))
            }
        }
    },
    _embedVod:{
        value: function(channelNode) {
            window.open('https://kick.com/'+channelNode.dataset.channelName+'?clip='+channelNode.dataset.id, '_blank');
        }
    },   
    _startFlipster:{
        value: function(wrapper,target) {
            var numberOnline = target.querySelectorAll('.cp-stream--online').length;
            var startAt = 2;
            switch(numberOnline) {
                case 1:
                    startAt = 0;
                    break;
                case 2:
                    startAt = 1;
                    break;
            }
            jQuery(wrapper).flipster({
                style: 'carousel',
                itemContainer: '.cp-streamweasels-kick__streams',
                itemSelector: '.cp-stream',
                loop: true,
                buttons: true,
                spacing: -0.5,
                scrollwheel: false,
                start: startAt,
                onItemSwitch: function() {
                    var activeEmbed = wrapper.querySelector('.flipster__item--embed')
                    var slidePast = wrapper.querySelector('.flipster__item--past-1')
                    var slideFuture = wrapper.querySelector('.flipster__item--future-1')
                    if (activeEmbed) {
                        var activeiFrame = activeEmbed && activeEmbed.querySelector('iframe')
                        var activeiPast = slidePast && slidePast.querySelector('iframe')
                        var activeiFuture = slideFuture && slideFuture.querySelector('iframe')
                        activeEmbed.classList.remove('flipster__item--embed')
                        if (activeiFrame) {
                            activeiFrame.remove()
                        }
                        if (activeiPast) {
                            activeiFrame.remove()
                        }	
                        if (activeiFuture) {
                            activeiFrame.remove()
                        }							
                    }
                }
            });
        }
    },        
    _roundViewers:{
        value: function(viewers) {
            if (viewers > 1000 && viewers < 999999) {
                viewers = (viewers / 1000).toFixed(1) + 'K';
                if (viewers.slice(viewers.length - 3) == '.0K') {
                    viewers = viewers.replace('.0K', 'K')
                }                
            } 
            return viewers;
        }
    },
    _daysAgo:{
        value: function(date) {
            const now = new Date();
            // Mimick a backend date
            var daysAgo = new Date(date);
            daysAgo.setDate(daysAgo.getDate());
            // Compare both, outputs in miliseconds
            var ago = now - daysAgo;
            var ago = Math.floor(ago / 1000);
            var part = 0;
            if (ago < 2) { return "a moment ago"; }
            if (ago < 5) { return "moments ago"; }
            if (ago < 60) { return ago + " seconds ago"; }    
            if (ago < 120) { return "a minute ago"; }
            if (ago < 3600) {
              while (ago >= 60) { ago -= 60; part += 1; }
              return part + " minutes ago";
            }
            if (ago < 7200) { return "an hour ago"; }
            if (ago < 86400) {
              while (ago >= 3600) { ago -= 3600; part += 1; }
              return part + " hours ago";
            }    
            if (ago < 172800) { return "a day ago"; }
            if (ago < 604800) {
              while (ago >= 172800) { ago -= 172800; part += 1; }
              return part + " days ago";
            }
            if (ago < 1209600) { return "a week ago"; }
            if (ago < 2592000) {
              while (ago >= 604800) { ago -= 604800; part += 1; }
              return part + " weeks ago";
            }
            if (ago < 5184000) { return "a month ago"; }
            if (ago < 31536000) {
              while (ago >= 2592000) { ago -= 2592000; part += 1; }
              return part + " months ago";
            }
            if (ago < 1419120000) { // 45 years, approximately the epoch
              return "more than year ago";
            }
        }
    },
    _modalControls:{
        value: function(modal) {
            modal.addEventListener('click', function(e) {
                modal.remove();
            })
            document.onkeydown = function(e){
                if(e.key === 'Escape'){
                    modal.remove();
                }
            }
        }
    },
    _embedTitle:{
        value: function(title) {
            var titleWrapper = this.wrapper.querySelector('.cp-streamweasels-kick__title');
            titleWrapper.innerHTML = '<p>'+title+'</p>';
        }
    },    
    _offlineMessage: {
        value: function() {
            var offlineHTML =
            `<div class="cp-streamweasels-kick__offline">
                ${this.showOfflineImage && "<img src='"+this.showOfflineImage+"'>"}
                ${this.showOfflineText && "<h3>"+this.showOfflineText+"</h3>"}
            </div>`;
            this.wrapper.querySelector('.cp-streamweasels-kick__offline-wrapper').innerHTML = '';
            this.wrapper.querySelector('.cp-streamweasels-kick__offline-wrapper').insertAdjacentHTML('beforeend', offlineHTML)            
        }
    },    
})

var streamWeaselsKickNodes = document.querySelectorAll('.cp-streamweasels-kick');
streamWeaselsKickNodes.forEach(function(item, index, array) {
    var uuid = item.dataset.uuid;
    var streamWeaselsKickVarsUuid = eval('streamWeaselsKickVars'+uuid);
    var channelsLength = ((streamWeaselsKickVarsUuid.channels).indexOf(',')) ? (streamWeaselsKickVarsUuid.channels.split(',')).length : 1;
    var streamWeaselsKickInit = new streamWeaselsKick({
        uuid: uuid,
        channels: streamWeaselsKickVarsUuid.channels,
        channelsLength: channelsLength,
        limit: streamWeaselsKickVarsUuid.limit,
        layout: streamWeaselsKickVarsUuid.layout,
        embed: streamWeaselsKickVarsUuid.embed,
        embedTitle: streamWeaselsKickVarsUuid.embedTitle,
        embedTitlePosition: streamWeaselsKickVarsUuid.embedTitlePosition,
        embedMuted: streamWeaselsKickVarsUuid.embedMuted,
        showOffline: streamWeaselsKickVarsUuid.showOffline,
        showOfflineText: streamWeaselsKickVarsUuid.showOfflineText,
        showOfflineImage: streamWeaselsKickVarsUuid.showOfflineImage,
        autoplay: streamWeaselsKickVarsUuid.autoplay,
        autoplaySelect: streamWeaselsKickVarsUuid.autoplaySelect,
        featured: streamWeaselsKickVarsUuid.featured,
        offlineImage: streamWeaselsKickVarsUuid.offlineImage,
        logoImage: streamWeaselsKickVarsUuid.logoImage,
        profileImage: streamWeaselsKickVarsUuid.profileImage,
        tileLayout: streamWeaselsKickVarsUuid.tileLayout,
        tileSorting: streamWeaselsKickVarsUuid.tileSorting,
        tileLive: streamWeaselsKickVarsUuid.tileLive,
        logoBgColour: streamWeaselsKickVarsUuid.logoBgColour,
        logoBorderColour: streamWeaselsKickVarsUuid.logoBorderColour,
        maxWidth: streamWeaselsKickVarsUuid.maxWidth,
        tileBgColour: streamWeaselsKickVarsUuid.tileBgColour,
        tileTitleColour: streamWeaselsKickVarsUuid.tileTitleColour,
        tileSubtitleColour: streamWeaselsKickVarsUuid.tileSubtitleColour,
        tileRoundedCorners: streamWeaselsKickVarsUuid.tileRoundedCorners,
        hoverColour: streamWeaselsKickVarsUuid.hoverColour,

    })
})