/**
 * Created with IntelliJ IDEA.
 * User: adi
 * Date: 10/17/13
 * Time: 1:47 PM
 * To change this template use File | Settings | File Templates.
 */
(function ($) {
    'use strict';
    var Rating = {},
        defaults = {
            itemId: null,
            rateSuccess: null,
            container: null
        };
    Rating = function (opts) {
        var self = this,
            loaded = false,
            handles = opts.container.find('a'),
            info = $(opts.infoClass);
        this.init = function () {
            if(!loaded)
                self.remoteCall({cmd: 'get', item_id: opts.itemId});
            self.handleEvents();
        };
        this.handleEvents = function () {
            handles.off('.rating').on('click.rating', self.rate);
        };
        this.rate = function (e) {
            var rating = e.currentTarget.innerHTML,
                remoteCallData = {cmd: 'set', item_id: opts.itemId, rating: rating, user_id: opts.userId};
            if(!/^\d+$/.test(rating))
                return false;
            return  self.remoteCall(remoteCallData);
        };
        this.remoteCall = function (data) {
            $.get(opts.remoteService, data, function (json) {
                if(json.error === undefined) {
                    var rating = (json.success.data.rating * 25);
                    opts.container.find('.current-rating').css('width', rating + 'px');
                    if(info.length) {
                        $('.rating', info).text(json.success.data.rating);
                        $('.counter', info).text(json.success.data.counter);
                    }
                    if(typeof opts.rateSuccess === 'function')
                        opts.rateSuccess.apply(this);
                }
            });
        }
    };
    $.fn.extend({
        rating: function (options) {
            var opts = $.extend({}, defaults, options);
            return this.each(function () {
                opts.container = $(this);
                opts.itemId = opts.container[0].id;
                (new Rating(opts)).init();
            });
        }
    });
}(jQuery));
