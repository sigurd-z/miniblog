{% if site.tags.length > 0 %}
  <div class="widget-wrap">
    <h3 class="widget-title">Tags</h3>
    <div class="widget-body">
      {{ list_tags() }}
    </div>
  </div>
{% endif %}