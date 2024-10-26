
<section class="slide-option">
    <div id="infinite-ticker">
            <ul id="ticker-list" class="highway-lane-ticker">
                @foreach($tickers as $ticker)
                <li class="highway-car-ticker">
                    <a href="{{ $ticker->url }}">
                        <p class="text-white">{{$ticker->tickerTranslation->name}}</p>
                    </a>
                </li>
                @endforeach
                <!-- Duplicate the items for seamless looping -->
                @foreach($tickers as $ticker)
                <li class="highway-car-ticker">
                    <a href="{{ $ticker->url }}">
                        <p class="text-white">{{$ticker->tickerTranslation->name}}</p>
                    </a>
                </li>
                @endforeach
            </ul>
    </div>
</section>
<style>
    #infinite-ticker {
    background-color: #003465;
    overflow: hidden;
    position: relative;
    height: auto;
}

#infinite-ticker ul {
    margin: 0;
}

.highway-lane-ticker {
    display: flex;
    animation: scroll 10s linear infinite; /* Changed to 10 seconds for faster speed */
}

.highway-car-ticker {
    min-width: 180px; /* Set a fixed width for each brand */
    display: flex;
    justify-content: center;
    align-items: center;
}

@keyframes scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-100%); /* Scroll half the width of the list */
    }
}
</style>
@push("script-ticker")
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tickerList = document.getElementById('ticker-list');
    const totalTickers = tickerList.children.length; // Count total tickers
    const tickerWidth = 180; // Set this to match your .highway-car-ticker width

    // Duplicate the ticker items for seamless scrolling
    for (let i = 0; i < totalTickers; i++) {
        const clone = tickerList.children[i].cloneNode(true);
        tickerList.appendChild(clone);
    }

    // Speed factor: Adjust this value to control speed (larger number = slower scrolling)
    const speedFactor = 0.25; // Adjust to control speed (1 is default, increase for slower, decrease for faster)

    // Calculate total width for animation duration and apply speed factor
    const totalWidth = tickerWidth * totalTickers * 2;
    const animationDuration = (totalWidth / 100) * speedFactor; // Lower speedFactor = faster speed
console.log(animationDuration);
    if (animationDuration > 0) {
        // Apply the calculated animation duration in seconds
        document.querySelector('.highway-lane-ticker').style.animationDuration = `${animationDuration}s`;
    }
});
</script>
@endpush
