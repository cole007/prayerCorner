# Plugin for users to subscribe to content updates via email

Sample code:

    {% set message = craft.session.getFlash('message') %}
    
        {% if message | length %}
    
        <p>{{ message }}</p>
    
        {% else %}
    
        <form method="post" action="" accept-charset="UTF-8">
            <fieldset>
            {{ getCsrfInput() }}
    
            <input type="hidden" name="action" value="prayerCorner/subscribe">
            <input type="hidden" name="redirect" value="{{ craft.request.path }}">
            <input type="hidden" name="entryId" value="{{ entry.id }}">
    
            {% if error is defined %}
                <p>{{ error }} </p>
            {% endif %}
    
    
            <h3><label for="fromEmail">Your Email</label></h3>
            <input id="fromEmail" type="text" name="fromEmail" value="{% if email is defined %}{{ email }}{% endif %}">
    
            <input type="submit" value="Send">
            </fieldset>
    
        </form>
        {% endif %}


# TO DO:

- move controllers into services




