<div class="container-fluid p-0">
    <div class="full-width-background">
        <img src="{{ $image }}" alt="Banner Image" style="width: 100%; height: 250px; object-fit: cover;">
    </div>
</div>

<style>
.full-width-background {
    width: 100vw; /* 100% of the viewport width */
    height: 250px; /* Adjust height as needed */
    margin: 0; /* Remove default margin */
    padding: 0; /* Remove default padding */
    overflow: hidden; /* Ensure content doesn't overflow */
}

.section {
    width: 80%; /* Content width of the regular sections */
    margin: 0 auto; /* Center the section */
    padding: 20px 0; /* Add padding for spacing */
}
</style>