{% if site.categories.length > 0 %}
  <div class="widget-wrap">
    <h3 class="widget-title">Categories</h3>
    <div class="widget-body">
      {{ list_categories() }}
    </div>
  </div>
{% endif %}