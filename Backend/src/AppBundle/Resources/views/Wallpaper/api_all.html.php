<?php 
$list=array();
foreach ($wallpapers as $key => $wallpaper) {
	$a = array();

	$a["id"]=$wallpaper->getId();
	$a["kind"]=$wallpaper->getType();
	$a["title"]=$wallpaper->getTitle();
	$a["description"]=$wallpaper->getDescription();
	$a["review"]=$wallpaper->getReview();
	$a["premium"]=$wallpaper->getPremium();
	$a["color"]=$wallpaper->getColor();
	$a["size"]=$wallpaper->getSize();
	$a["resolution"]=$wallpaper->getResolution();
	$a["comment"]=$wallpaper->getComment();
	$a["comments"]=sizeof($wallpaper->getComments());
	$a["downloads"]=$wallpaper->getDownloads();
	$a["views"]=$wallpaper->getViews();
	$a["shares"]=$wallpaper->getShares();
	$a["sets"]=$wallpaper->getSets();
	$a["trusted"]=$wallpaper->getUser()->getTrusted();
	$a["user"]=$wallpaper->getUser()->getName();
	$a["userid"]=$wallpaper->getUser()->getId();
	$a["userimage"]=$wallpaper->getUser()->getImage();
	if ($wallpaper->getVideo()) {
		$a["type"]=$wallpaper->getVideo()->getType();
		$a["extension"]=$wallpaper->getVideo()->getExtension();
	}else{
		$a["type"]=$wallpaper->getMedia()->getType();
		$a["extension"]=$wallpaper->getMedia()->getExtension();
	}
	$a["thumbnail"]= $this['imagine']->filter($view['assets']->getUrl($wallpaper->getMedia()->getLink()), 'wallpaper_thumb_api');
	$a["image"]= $this['imagine']->filter($view['assets']->getUrl($wallpaper->getMedia()->getLink()), 'wallpaper_image_api');
	if ($wallpaper->getVideo()) {
		if ($wallpaper->getVideo()->getEnabled()) {
			$a["original"] = $app->getRequest()->getSchemeAndHttpHost() . "/" .$wallpaper->getVideo()->getLink();
		}else{
			$a["original"] = $wallpaper->getVideo()->getLink();
		}	
	}else{
		$a["original"]=$app->getRequest()->getSchemeAndHttpHost() . "/" . $wallpaper->getMedia()->getLink();
	}
	$a["created"]=$view['time']->diff($wallpaper->getCreated());
	$a["tags"]=$wallpaper->getTags();

	$list[]=$a;
}
echo json_encode($list, JSON_UNESCAPED_UNICODE);?>