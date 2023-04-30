<?php 
$list=array();
$list_wallpapers=array();
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

	$list_wallpapers[]=$a;
}


$list_slide = array();
foreach ($slides as $key => $slide) {
	$s = null;
	$s["id"] = $slide->getId();
	$s["title"] = $slide->getTitle();
	$s["type"] = $slide->getType();
	$s["image"] = $this['imagine']->filter($view['assets']->getUrl($slide->getMedia()->getLink()), 'slide_thumb');
	if ($slide->getType() == 3 && $slide->getWallpaper() != null) {
		$wallpaper = $slide->getWallpaper();
        $w = array();
        $w["id"]=$wallpaper->getId();
        $w["kind"]=$wallpaper->getType();
        $w["title"]=$wallpaper->getTitle();
        $w["description"]=$wallpaper->getDescription();
        $w["review"]=$wallpaper->getReview();
        $w["premium"]=$wallpaper->getPremium();
        $w["color"]=$wallpaper->getColor();
        $w["size"]=$wallpaper->getSize();
        $w["resolution"]=$wallpaper->getResolution();
        $w["comment"]=$wallpaper->getComment();
        $w["comments"]=sizeof($wallpaper->getComments());
        $w["downloads"]=$wallpaper->getDownloads();
        $w["views"]=$wallpaper->getViews();
        $w["shares"]=$wallpaper->getShares();
        $w["sets"]=$wallpaper->getSets();
        $w["trusted"]=$wallpaper->getUser()->getTrusted();
        $w["user"]=$wallpaper->getUser()->getName();
        $w["userid"]=$wallpaper->getUser()->getId();
        $w["userimage"]=$wallpaper->getUser()->getImage();
        if ($wallpaper->getVideo()) {
            $w["type"]=$wallpaper->getVideo()->getType();
            $w["extension"]=$wallpaper->getVideo()->getExtension();
        }else{
            $w["type"]=$wallpaper->getMedia()->getType();
            $w["extension"]=$wallpaper->getMedia()->getExtension();
        }
        $w["thumbnail"]= $this['imagine']->filter($view['assets']->getUrl($wallpaper->getMedia()->getLink()), 'wallpaper_thumb_api');
        $w["image"]= $this['imagine']->filter($view['assets']->getUrl($wallpaper->getMedia()->getLink()), 'wallpaper_image_api');
        if ($wallpaper->getVideo()) {
            if ($wallpaper->getVideo()->getEnabled()) {
                $w["original"] = $app->getRequest()->getSchemeAndHttpHost() . "/" .$wallpaper->getVideo()->getLink();
            }else{
                $w["original"] = $wallpaper->getVideo()->getLink();
            }   
        }else{
            $w["original"]=$app->getRequest()->getSchemeAndHttpHost() . "/" . $wallpaper->getMedia()->getLink();
        }
        $w["created"]=$view['time']->diff($wallpaper->getCreated());
        $w["tags"]=$wallpaper->getTags();
		$s["wallpaper"] = $w;
	} elseif ($slide->getType() == 1 && $slide->getCategory() != null) {
		$c["id"] = $slide->getCategory()->getId();
		$c["title"] = $slide->getCategory()->getTitle();
		$c["image"] = $this['imagine']->filter($view['assets']->getUrl($slide->getCategory()->getMedia()->getLink()), 'category_thumb_api');
		$s["category"] = $c;
	} elseif ($slide->getType() == 2 && $slide->getUrl() != null) {
		$s["url"] = $slide->getUrl();
	}
	$list_slide[] = $s;
}

$list["categories"]=$categories;
$list["slides"]=$list_slide;
$list["packs"]=$packs;
$list["wallpapers"]=$list_wallpapers;
echo json_encode($list, JSON_UNESCAPED_UNICODE);?>