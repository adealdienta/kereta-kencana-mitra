<div class="photo-frame-caption">
    <div>@if(!empty($imgMeta['icon']))<i class="{{ $imgMeta['icon'] }}" style="color: #d4d4d8;"></i> @endif{{ $imgMeta['caption'] ?? '' }}</div>
    @if(!empty($imgMeta['updated_at']) && !empty($imgMeta['updated_at_label']))
        <div class="photo-frame-updated">
            Diperbarui: <time datetime="{{ $imgMeta['updated_at'] }}">{{ $imgMeta['updated_at_label'] }}</time>
        </div>
    @endif
</div>
