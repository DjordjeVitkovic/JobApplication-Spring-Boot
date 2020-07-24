var thSmartSidemenu = function($el, options){
    var context             = this;
    context.$sidemenu       = jQuery($el);
    context.min             = options.min || 2;
    context.max             = options.max || 3;
    context.extendedSearch  = options.extendedSearch || false;
    context.elements        = [];
    context.$container      = jQuery(options.container).length ? jQuery(options.container) : jQuery('body');
    context.id              = context.$sidemenu.attr('id') || context.$sidemenu.attr('id', this.getUniqueId()).attr('id');
    context.build();
}
thSmartSidemenu.prototype   = {
    build                   : function(){
        var context             = this;
        context.elements        = context.getElementsFrom( context.min );
        context.buildStructure();
        
        return this;
    },
    buildStructure          : function(){
        if( this.elements && this.elements.length ){
            this.$root       = this.$root || jQuery('<ul>')
                .addClass('th-smart-sidemenu nav')
                .empty()
                .appendTo(this.$sidemenu)
            ;
            this.buildSubStructure( this.$root, this.elements );
            jQuery('body').scrollspy({
                target: '#' + this.id
            });
        }
    },
    buildSubStructure       : function( $root, items ){
        var context             = this;
        if( items && items.length ){
            jQuery.each( items, function(i, item){
                var $el = jQuery('<li>')
                    .append( jQuery('<a>')
                        .text( item.$el.text() )
                        .attr('href', '#' + ( item.$el.attr('id') || item.$el.attr('id', context.getUniqueId() ).attr('id') ) )
                        )
                    .appendTo( $root )
                ;

                if( item.sub && item.sub.length ){
                    context.buildSubStructure( jQuery('<ul>').addClass('nav').appendTo($el), item.sub );
                }
            });
        }
    },
    getUniqueId             : function(){
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }
        var id = s4() + s4() + '-' + s4() + '-' + s4() + '-' +
            s4() + '-' + s4() + s4() + s4();
        while( jQuery('#' + id).length ) {
            id = s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                s4() + '-' + s4() + s4() + s4();
        }
        return id;
    },
    getStopSelector         : function(next){
        var sel = [];
        for (var i = 1; i < next; i++) {
            sel.push(' h' + i);
        };
        return sel.join(', ');
    },
    getElementsFrom         : function(lvl, $curr, next){
        if( lvl > this.max || lvl < this.min ){
            return false;
        }
        if( next > this.max || next < this.min ){
            return false;
        }
        var
            context             = this,
            $els                = false
        ;
        next                    = next || lvl + 1;
        if( $curr ){
            $els                = $curr.nextUntil( context.getStopSelector(next), 'h' + next );
        }else{
            $els                = context.$container.find('h' + lvl)
        }
        if( !$els || !$els.length ){
            if( context.extendedSearch ){
                if( $curr ){
                    return context.getElementsFrom( lvl, $curr, ++next );
                }else{
                    return context.getElementsFrom( ++lvl );
                }
            }
            return false
        }

        var ret                 = [];
        $els.each(function(i, $el){
            $el                 = jQuery($el);
            ret.push({
                $el     : $el,
                sub     : context.getElementsFrom($curr ? next : lvl, $el )
            })
        });
        return ret;
    }
};

jQuery(function(){
    jQuery('[data-smart-sidemenu]').each(function(i, $el){
        $el = jQuery($el);
        var options = {
            min         : $el.data('ssMin') || 2,
            max         : $el.data('ssMax') || 3,
            container   : $el.data('smartSidemenu')
        }
        $el.data('thSmartSidemenu', new thSmartSidemenu($el, options));
    });
});