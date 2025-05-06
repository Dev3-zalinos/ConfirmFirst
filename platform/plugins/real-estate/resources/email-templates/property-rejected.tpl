{{ header }}

<p>Hello <strong>{{ author_name }}</strong>,</p>

<p>Your property <strong>{{ property_name }}</strong> has been successfully rejected on {{ site_title }}.</p>

<p>The reason for rejection is as follows: <strong>{{ reason }}</strong>. If you believe this decision was made in error, please contact our support team at <a href="mailto:{{ site_email }}">{{ site_email }}</a>.</p>

<p>To view or edit your property, please click on this link: <a href="{{ property_link }}">View Property</a></p>

<p>Regards,</p>

<p><strong>{{ site_title }}</strong></p>

{{ footer }}
