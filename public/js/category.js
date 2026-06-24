function changeSort(strategy) {
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.set("sort", strategy);
  window.location.search = urlParams.toString();
}
