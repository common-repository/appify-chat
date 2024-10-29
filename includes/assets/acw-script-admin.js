const handleToast = (show, message) => {
  const toastContainer = document.querySelector(".toast-container");
  if (!toastContainer) return;

  if (show) {
    toastContainer.querySelector(".toast-body").innerHTML = message;
    toastContainer?.classList.remove("display-none");
    setTimeout(() => {
      toastContainer?.classList.add("display-none");
    }, 3000);
  } else {
    toastContainer?.classList.add("display-none");
  }
};

const handlePreview = () => {
  console.log("Preview function called");
  const previewContainer = document.getElementById("preview-container");
  const previewStyle = document.getElementById("acw-style");

  if (previewContainer) previewContainer.innerHTML = ""; // Clear existing preview
  if (previewStyle) previewStyle.remove();

  const id = document.querySelector("#acw-id").value;
  const number = document.querySelector("#acw-number").value;
  const message = document.querySelector("#acw-message").value;
  console.log(message);
  const position = document.querySelector("#acw-position").value;
  const horizontal_space = document.querySelector("#acw-hs").value;
  const vertical_space = document.querySelector("#acw-vs").value;
  const radius = document.querySelector("#acw-corner-radius").value;
  const icon = document.querySelector("#acw-icon").value;
  const mobile_link = document.querySelector("#acw-mobile-link").value;
  const box_tb_padding = document.querySelector(
    "#acw-msg-box-tb-padding"
  ).value;
  const box_lr_padding = document.querySelector(
    "#acw-msg-box-lr-padding"
  ).value;
  const box_radius = document.querySelector("#acw-msg-box-radius").value;
  const box_bgcolor = document.querySelector("#acw-msg-box-bgcolor").value;
  const box_font_color = document.querySelector(
    "#acw-msg-box-font-color"
  ).value;
  const box_shadow_color = document.querySelector(
    "#acw-msg-box-shadow-color"
  ).value;

  const settings = {
    id,
    number,
    message,
    position,
    horizontal_space,
    vertical_space,
    radius,
    icon,
    mobile_link,
    box_tb_padding,
    box_lr_padding,
    box_radius,
    box_bgcolor,
    box_font_color,
    box_shadow_color,
  };

  acw(settings, previewContainer);
};

const loadData = () => {
  console.log("Loading data...", document.querySelector("#acw-icon-button"));
  // if (document.querySelector("#acw-icon-button") === null) {
  //   console.log("Button not found");
  //   // setTimeout(loadData, 200);
  //   return;
  // }
  const positionValue = document.querySelector("#acw-position-value").value;
  document.querySelector("#acw-position").value = positionValue;

  const mobileLinkVal = document.querySelector("#acw-mobile-link-value").value;
  const mobileLinks = document.getElementsByName("acw-mobile-link");
  mobileLinks.forEach((radio) => {
    if (radio.value === mobileLinkVal) radio.checked = true;
  });

  const icon = document.querySelector("#acw-icon");
  if (icon && icon.value !== "") {
    hideShowIcon();
  }

  const mediaUploadBtn = document.querySelector("#acw-icon-button");
  mediaUploadBtn.addEventListener("click", handleMediaSelect);

  handlePreview();
};

const handleOnInput = (key, suffix = "") => {
  const value = document.querySelector(`#${key}`).value;
  document.querySelector(`#${key}-value`).textContent = value + suffix;
  handlePreview();
};

const handleRemoveIcon = () => {
  const preview = document.querySelector("#acw-icon-preview");
  preview.src = "";
  document.querySelector("#acw-icon").value = ""; // Clear the file input
  hideShowIcon();
};

const hideShowIcon = () => {
  const iconPreview = document.querySelector("#image_preview_container");
  iconPreview.classList.toggle("display-none");
  handlePreview();
};

var mediaUploader;
const handleMediaSelect = (e) => {
  e.preventDefault();
  if (mediaUploader) {
    mediaUploader.open();
    return;
  }

  mediaUploader = wp.media({
    title: "Select Custom Icon",
    button: {
      text: "Use this image",
    },
    multiple: false,
  });

  mediaUploader.on("select", () => {
    const attachment = mediaUploader.state().get("selection").first().toJSON();
    document.querySelector("#acw-icon").value = attachment.url;

    const previewImage = document.querySelector("#acw-icon-preview");
    previewImage.src = attachment.url;

    const iconPreview = document.getElementById("image_preview_container");
    iconPreview.classList.remove("display-none");

    handlePreview();
  });

  mediaUploader.open();
};

document.addEventListener("DOMContentLoaded", loadData);
