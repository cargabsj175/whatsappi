jQuery(document).ready(function($) {
    const $container = $('.wa-pro-container');
    const $button = $('.wa-pro-button');
    const $bubble = $('.wa-pro-bubble');
    const $input = $('.wa-pro-message');
    const $send = $('.wa-pro-send');
    
    let isOpen = false;
    
    // Toggle bubble
    function toggleBubble() {
        isOpen = !isOpen;
        $bubble.toggleClass('active', isOpen);
        if(isOpen) {
            $input.trigger('focus');
            $(document).on('click', closeOnClickOutside);
        } else {
            $(document).off('click', closeOnClickOutside);
        }
    }
    
    // Cerrar al hacer click fuera
    function closeOnClickOutside(e) {
        if(!$(e.target).closest($container).length) {
            toggleBubble();
        }
    }
    
    // Event handlers
    $button.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleBubble();
    });
    
    $send.on('click', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    $input.on('keypress', function(e) {
        if(e.which === 13) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Función para enviar mensaje
    function sendMessage() {
        const message = $input.val().trim() || wa_pro.default_msg;
        const phone = wa_pro.phone;
        
        if(!phone) {
            console.error('Número de WhatsApp no configurado');
            return;
        }
        
        const encodedMessage = encodeURIComponent(message);
        const whatsappURL = `https://wa.me/${phone}?text=${encodedMessage}`;
        
        window.open(whatsappURL, '_blank');
        
        // Reset
        $input.val('');
        toggleBubble();
    }
});