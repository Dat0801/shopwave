export function getImageUrl(path, width = 600, height = 400) {
    if (path) {
        if (path.startsWith('http') || path.startsWith('data:')) {
            return path;
        }
        return '/storage/' + path;
    }
    return `https://placehold.co/${width}x${height}?text=No+Image`;
}
