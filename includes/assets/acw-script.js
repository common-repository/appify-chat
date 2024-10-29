const acw = (settings, previewContainer, isPreview = true) => {
  // const currentURL = window.location.href;
  const {
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
  } = settings;

  const acwClick = () => {
    const ua = navigator.userAgent;
    const userDevice =
      /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua);

    const mediaQuery = "only screen and (max-width: 760px)";
    const isMobile = window.matchMedia(mediaQuery).matches;

    const text = `text=${encodeURIComponent(message)}`;
    const phone = `phone=${number}`;
    let url = `https://web.whatsapp.com/send?${phone}&${text}&app_absent=0`;

    if (userDevice || isMobile) {
      url =
        mobile_link === "whatsapp://"
          ? `whatsapp://send?phone=${number}&${text}`
          : `https://wa.me/${number}?${text}`;
    }

    window.open(url, "_blank");
  };

  // Made a default icon URL dynamically
  // const commonPath = currentURL.split("/").filter(Boolean)[3];
  // const baseUrl = currentURL.split(`/${commonPath}/`)[0];
  // const siteUrl = document.querySelector("#acw_site_url")?.value || "";
  const subUrl = "/wp-content/plugins/appify-chat/includes/assets/acw-icon.png";
  const iconUrl = icon || `${acw_site_url}${subUrl}`;

  document.head.innerHTML += `<style id="acw-style">
    .acw_container {
      position: fixed;
      display: flex;
      z-index: 999999;
      margin: ${vertical_space}px ${horizontal_space}px;
    }
    .acw_bottom_right {
      bottom: 0px;
      right: 0px;
    }
    .acw_bottom_left {
      bottom: 0px;
      left: 0px;
    }
    .acw_top_right {
      top: 0px;
      right: 0px;
    }
    .acw_top_left {
      top: 0px;
      left: 0px;
    }
    .acw_message_bottom_right, .acw_message_top_right {
      margin-right: 10px;
      right: 50px;
    }
    .acw_message_bottom_left, .acw_message_top_left {
      margin-left: 10px;
      order: 2;
      left: 50px;
    }
    .acw_message_bottom_right, .acw_message_bottom_left {
      bottom: 0px;
    }
    .acw_image {
      border-radius: ${radius}px;
      cursor: pointer;
    }
    .acw_message {
      display: none;
      position: absolute;
      background: ${box_bgcolor};
      color: ${box_font_color};
      padding: ${box_tb_padding}px ${box_lr_padding}px;
      border-radius: ${box_radius}px;
      max-width: 20vw;
      font-family: inherit;
      box-shadow: 3px 3px 5px 0px ${box_shadow_color};
      width: 250px;
      font-size: 16px;
      line-height: 22px;
    }
    .acw_container:hover .acw_message {
      display: block;
    }
  </style>`;

  const UI = `
    <div class="acw_container acw_${position}">
      <div class="acw_message acw_message_${position}">${message}</div>
      <img id="acw_image" class="acw_image" src="${iconUrl}" alt="acw_appifychat_icon" width="50" height="50" loading="lazy">
    </div>`;

  if (isPreview) {
    previewContainer.innerHTML = UI;
  } else {
    previewContainer.insertAdjacentHTML("beforeend", UI);
  }
  const iconContainer = previewContainer.querySelector(".acw_container");
  iconContainer.addEventListener("click", acwClick);
};

if (
  typeof acw_settings !== "undefined" &&
  acw_settings !== null &&
  acw_settings !== ""
) {
  if (window.hasOwnProperty("acw_settings")) {
    const wpwd = acw_settings;
    if (wpwd !== undefined && wpwd !== null && wpwd !== "") {
      acw(wpwd, document.body, false);
    }
  }
}
