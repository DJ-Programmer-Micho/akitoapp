<div class=" p-0">
    <div class="full-width-background">
        <img loading="lazy" src="{{ app('cloudfront') . $image }}" alt="Akitu_Banner_Image" style="width: 100%; max-height: 300px; object-fit: cover;">
    </div>
</div>

<style>
.full-width-background {
    width: 100vw; /* 100% of the viewport width */
    max-height: 300px; /* Adjust height as needed */
    margin: 0; /* Remove default margin */
    padding: 0; /* Remove default padding */
    /* overflow: hidden; Ensure content doesn't overflow */
}

.section {
    width: 80%; /* Content width of the regular sections */
    margin: 0 auto; /* Center the section */
    padding: 20px 0; /* Add padding for spacing */
}
</style>