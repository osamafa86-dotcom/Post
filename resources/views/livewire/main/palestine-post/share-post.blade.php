<div class="share-article">
    <h4 class="btn-share">
        {{__('messages.share')}}
        <i class="fa-solid fa-share"></i>
    </h4>
    <div class="share-social-icons social-icons">
        <ul>
            <li>
                <a wire:click.prevent="share('facebook')" id="{{rand(0000,9999)}}">
                    <img src="{{ asset('assets/main/palestine_post/imgs/64px-2021_Facebook_icon.svg.png') }}"
                         alt="Facebook"/>
                </a>
            </li>
            <li>
                <a wire:click.prevent="share('twitter')" id="{{rand(0000,9999)}}">
                    <img src="{{ asset('assets/main/palestine_post/imgs/x-logo.svg') }}"
                         alt="X"/>
                </a>
            </li>
            <li>
                <a wire:click.prevent="share('whatsapp')" id="{{rand(0000,9999)}}">
                    <img src="{{ asset('assets/main/palestine_post/imgs/whatsapp.png') }}"
                         alt="WhatsApp"/>
                </a>
            </li>
            <li>
                <a wire:click.prevent="share('telegram')" id="{{rand(0000,9999)}}">
                    <img src="{{ asset('assets/main/palestine_post/imgs/64px-Telegram_logo.svg.png') }}"
                         alt="Telegram"/>
                </a>
            </li>
        </ul>

    </div>
    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on("updateMetaData.{{$this->componentID}}", (data) => {
                const {title, description, image,share_url} = data[0];
                // console.log(share_url);
                // console.log(image);
                // console.log(title);
                // console.log(description);

                window.pageMeta = {
                    title: title,
                    description: description,
                    image: image,
                    url: share_url
                };
                (function () {
                    const meta = window.pageMeta;

                    if (meta.title) {
                        document.title = meta.title;

                        setMeta('name', 'title', meta.title);
                        setMeta('property', 'og:title', meta.title);
                        setMeta('property', 'twitter:title', meta.title);
                    }

                    if (meta.description) {
                        setMeta('name', 'description', meta.description);
                        setMeta('property', 'og:description', meta.description);
                        setMeta('property', 'twitter:description', meta.description);
                    }

                    if (meta.image) {
                        setMeta('property', 'og:image', meta.image);
                        setMeta('property', 'twitter:image', meta.image);
                        setMeta('property', 'twitter:card', 'summary_large_image');
                    }

                    setMeta('property', 'og:type', 'website');
                    setMeta('property', 'og:url', meta.url);
                    setMeta('property', 'twitter:url', meta.url);

                    function setMeta(attrName, attrValue, content) {
                        let selector = `meta[${attrName}="${attrValue}"]`;
                        let tag = document.querySelector(selector);

                        if (!tag) {
                            tag = document.createElement('meta');
                            tag.setAttribute(attrName, attrValue);
                            document.head.appendChild(tag);
                        }

                        tag.setAttribute('content', content);
                    }
                })();
                window.open(share_url, "_blank");
            });
        });

    </script>
</div>
