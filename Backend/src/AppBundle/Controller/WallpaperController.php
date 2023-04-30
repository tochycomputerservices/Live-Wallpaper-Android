<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Rate;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Wallpaper;
use AppBundle\Form\GifType;
use AppBundle\Form\ImageType;
use AppBundle\Form\VideoType;
use AppBundle\Form\VideoTypeUrl;
use AppBundle\Form\WallpaperReviewType;
use MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class WallpaperController extends Controller {
	private $colors = array("F8B195", "F67280", "C06C84", "6C5B7B", "355C7D", "F25157", "FF8B8B", "005778", "0378A6", "0378A6", "1B7F79", "747F7F", "FF4858");
	public function formatNumber($size) {
		if ($size < 1000) {
			return $size . ' B';
		} else {
			$size = $size / 1000;
			$units = ['KB', 'MB', 'GB', 'TB'];
			foreach ($units as $unit) {
				if (round($size, 2) >= 1000) {
					$size = $size / 1000;
				} else {
					break;
				}
			}
			return round($size, 2) . ' ' . $unit;
		}
	}
	public function getWallpaperColor($path) {
		$palette = $this->colorPalette($path, 7);
		if ($palette == null) {
			return "#0c2635";
		}
		if (sizeof($palette) == 7) {
			return $this->mixcolors($this->mixcolors($this->mixcolors($this->mixcolors($this->mixcolors($this->mixcolors($palette[0], $palette[1]), $palette[2]), $palette[3]), $palette[4]), $palette[5]), $palette[6]);
		} else if (sizeof($palette) == 6) {
			return $this->mixcolors($this->mixcolors($this->mixcolors($this->mixcolors($this->mixcolors($palette[0], $palette[1]), $palette[2]), $palette[3]), $palette[4]), $palette[5]);
		} else if (sizeof($palette) == 5) {
			return $this->mixcolors($this->mixcolors($this->mixcolors($this->mixcolors($palette[0], $palette[1]), $palette[2]), $palette[3]), $palette[4]);
		} else if (sizeof($palette) == 4) {
			return $this->mixcolors($this->mixcolors($this->mixcolors($palette[0], $palette[1]), $palette[2]), $palette[3]);
		} else if (sizeof($palette) == 3) {
			return $this->mixcolors($this->mixcolors($palette[0], $palette[1]), $palette[2]);
		} else if (sizeof($palette) == 2) {
			return $this->mixcolors($palette[0], $palette[1]);
		} else if (sizeof($palette) == 1) {
			return $palette[0];
		} else if (sizeof($palette) == 0) {
			return "#0c2635";
		}
	}
	function colorPalette($imageFile, $numColors, $granularity = 5) {
		$granularity = max(1, abs((int) $granularity));
		$colors = array();
		$size = @getimagesize($imageFile);
		if ($size === false) {
			user_error("Unable to get image size data" . $imageFile);
			return false;
		}
		$img = @imagecreatefromstring(file_get_contents($imageFile));
		if (!$img) {
			user_error("Unable to open image file");
			return false;
		}
		for ($x = 0; $x < $size[0]; $x += $granularity) {
			for ($y = 0; $y < $size[1]; $y += $granularity) {
				$thisColor = imagecolorat($img, $x, $y);
				$rgb = imagecolorsforindex($img, $thisColor);
				$red = round(round(($rgb['red'] / 0x33)) * 0x33);
				$green = round(round(($rgb['green'] / 0x33)) * 0x33);
				$blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
				$thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
				if (array_key_exists($thisRGB, $colors)) {
					$colors[$thisRGB]++;
				} else {
					$colors[$thisRGB] = 1;
				}
			}
		}
		arsort($colors);
		return array_slice(array_keys($colors), 0, $numColors);
	}
	function mixcolors($color1, $color2) {
		$c1_p1 = hexdec(substr($color1, 0, 2));
		$c1_p2 = hexdec(substr($color1, 2, 2));
		$c1_p3 = hexdec(substr($color1, 4, 2));

		$c2_p1 = hexdec(substr($color2, 0, 2));
		$c2_p2 = hexdec(substr($color2, 2, 2));
		$c2_p3 = hexdec(substr($color2, 4, 2));

		$m_p1 = sprintf('%02x', (round(($c1_p1 + $c2_p1) / 2)));
		$m_p2 = sprintf('%02x', (round(($c1_p2 + $c2_p2) / 2)));
		$m_p3 = sprintf('%02x', (round(($c1_p3 + $c2_p3) / 2)));

		return $m_p1 . $m_p2 . $m_p3;
	}
	public function fileResolution($file) {
		$dimensions = getimagesize($file);
		return $dimensions[0] . " X " . $dimensions[1];
	}
	public function addVideoAction(Request $request) {
		$wallpaper = new Wallpaper();
		$wallpaper->setType("video");
		$form = $this->createForm(new VideoType(), $wallpaper);
		$em = $this->getDoctrine()->getManager();
		$colors = $em->getRepository('AppBundle:Color')->findAll();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if ($wallpaper->getFile() != null and $wallpaper->getFilevideo() != null) {

				$size = $this->formatNumber($wallpaper->getFilevideo()->getClientSize());
				$resolution = $this->fileResolution($wallpaper->getFile());

				$media = new Media();
				$media->setFile($wallpaper->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));

				$wallpaper_media = new Media();
				$wallpaper_media->setFile($wallpaper->getFilevideo());
				$wallpaper_media->setEnabled(true);
				$wallpaper_media->upload($this->container->getParameter('files_directory'));

				$em->persist($media);
				$em->flush();

				$em->persist($wallpaper_media);
				$em->flush();

				$path = $this->container->getParameter('files_directory') . $media->getPath();
				$color = $this->getWallpaperColor($path);

				$wallpaper->setMedia($media);

				$wallpaper->setVideo($wallpaper_media);

				$wallpaper->setUser($this->getUser());
				$wallpaper->setReview(false);
				$wallpaper->setDownloads(0);
				$wallpaper->setSize($size);
				$wallpaper->setResolution($resolution);
				$wallpaper->setColor($color);

				$tags_list = explode(",", $wallpaper->getTags());
				foreach ($tags_list as $key => $value) {
					$tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
					if ($tag == null) {
						$tag = new Tag();
						$tag->setName(strtolower($value));
						$em->persist($tag);
						$em->flush();
						$wallpaper->addTagslist($tag);
					} else {
						$wallpaper->addTagslist($tag);
					}
				}

				$em->persist($wallpaper);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_wallpaper_index'));
			} else {
				$photo_error = new FormError("Required image file");
				$wallpaper_error = new FormError("Required video file");
				$form->get('file')->addError($photo_error);
				$form->get('filevideo')->addError($wallpaper_error);
			}
		}
		return $this->render("AppBundle:Wallpaper:video_add.html.twig", array("colors" => $colors, "form" => $form->createView()));
	}
	public function editVideoAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "review" => false));
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$colors = $em->getRepository('AppBundle:Color')->findAll();

		$tags = "";
		foreach ($wallpaper->getTagslist() as $key => $value) {
			if ($key == sizeof($wallpaper->getTagslist()) - 1) {
				$tags .= $value->getName();
			} else {
				$tags .= $value->getName() . ",";
			}
		}
		$wallpaper->setTags($tags);

		$form = $this->createForm(new VideoType(), $wallpaper);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$wallpaper->setTagslist(array());
			$em->persist($wallpaper);
			$em->flush();

			$tags_list = explode(",", $wallpaper->getTags());
			foreach ($tags_list as $k => $v) {
				$tags_list[$k] = strtolower($v);
			}
			$tags_list = array_unique($tags_list);

			foreach ($tags_list as $key => $value) {
				$tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
				if ($tag == null) {
					$tag = new Tag();
					$tag->setName(strtolower($value));
					$em->persist($tag);
					$em->flush();
					$wallpaper->addTagslist($tag);
				} else {
					$wallpaper->addTagslist($tag);
				}
			}

			if ($wallpaper->getFile() != null) {

				$resolution = $this->fileResolution($wallpaper->getFile());

				$media = new Media();
				$media_old = $wallpaper->getMedia();
				$media->setFile($wallpaper->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();
				$wallpaper->setMedia($media);
				$em->flush();
				$media_old->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old);
				$em->flush();

				$path = $this->container->getParameter('files_directory') . $media->getPath();
				$color = $this->getWallpaperColor($path);

				$wallpaper->setColor($color);
				$wallpaper->setResolution($resolution);
			}

			if ($wallpaper->getFilevideo() != null) {

				$size = $this->formatNumber($wallpaper->getFilevideo()->getClientSize());

				$wallpaper_media = new Media();
				$wallpaper_media_old = $wallpaper->getVideo();
				$wallpaper_media->setFile($wallpaper->getFilevideo());
				$wallpaper_media->setEnabled(true);
				$wallpaper_media->upload($this->container->getParameter('files_directory'));
				$em->persist($wallpaper_media);
				$em->flush();

				$wallpaper->setVideo($wallpaper_media);
				$em->flush();

				$wallpaper_media_old->delete($this->container->getParameter('files_directory'));
				$em->remove($wallpaper_media_old);
				$em->flush();

				$wallpaper->setSize($size);

			}

			$em->persist($wallpaper);
			$em->flush();
			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_wallpaper_index'));
		}
		return $this->render("AppBundle:Wallpaper:video_edit.html.twig", array("colors" => $colors, "form" => $form->createView()));
	}

	public function addVideoUrlAction(Request $request) {
		$wallpaper = new Wallpaper();
		$wallpaper->setType("video");
		$form = $this->createForm(new VideoTypeUrl(), $wallpaper);
		$em = $this->getDoctrine()->getManager();
		$colors = $em->getRepository('AppBundle:Color')->findAll();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$file_ext = substr(strrchr($wallpaper->getUrlvideo(), '.'), 1);
			switch ($file_ext) {
			case 'mp4':
				$file_type = "video/mp4";
				break;
			case 'webm':
				$file_type = "video/webm";
				break;
			default:
				$file_type = "none";
				break;
			}

			if ($file_type != "none") {
				if ($wallpaper->getFile() != null) {

					$size = $this->formatNumber($wallpaper->getFile()->getClientSize());
					$resolution = $this->fileResolution($wallpaper->getFile());

					$media = new Media();
					$media->setFile($wallpaper->getFile());
					$media->setEnabled(true);
					$media->upload($this->container->getParameter('files_directory'));
					$wallpaper->setMedia($media);

					$wallpaper_media = new Media();
					$wallpaper_media->setTitre($wallpaper->getTitle());
					$wallpaper_media->setUrl($wallpaper->getUrlvideo());
					$wallpaper_media->setExtension($file_ext);
					$wallpaper_media->setType($file_type);
					$wallpaper_media->setEnabled(false);

					$wallpaper->setVideo($wallpaper_media);

					$path = $this->container->getParameter('files_directory') . $media->getPath();
					$color = $this->getWallpaperColor($path);

					$wallpaper->setUser($this->getUser());
					$wallpaper->setReview(false);
					$wallpaper->setDownloads(0);
					if ($wallpaper->getSize() == "") {
						$wallpaper->setSize($size);
					}
					if ($wallpaper->getResolution() == "") {
						$wallpaper->setResolution($resolution);
					}
					$wallpaper->setColor($color);
					$em->persist($media);
					$em->flush();

					$path = $this->container->getParameter('files_directory') . $media->getPath();
					$color = $this->getWallpaperColor($path);

					$em->persist($wallpaper_media);
					$em->flush();

					$tags_list = explode(",", $wallpaper->getTags());
					foreach ($tags_list as $key => $value) {
						$tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
						if ($tag == null) {
							$tag = new Tag();
							$tag->setName(strtolower($value));
							$em->persist($tag);
							$em->flush();
							$wallpaper->addTagslist($tag);
						} else {
							$wallpaper->addTagslist($tag);
						}
					}

					$em->persist($wallpaper);
					$em->flush();
					$this->addFlash('success', 'Operation has been done successfully');
					return $this->redirect($this->generateUrl('app_wallpaper_index'));
				} else {
					$photo_error = new FormError("Required image file");
					$form->get('file')->addError($photo_error);
				}
			} else {
				$type_error = new FormError("Url has video not supported");
				$form->get('urlvideo')->addError($type_error);
			}
		}
		return $this->render("AppBundle:Wallpaper:video_add_url.html.twig", array("colors" => $colors, "form" => $form->createView()));
	}
	public function editVideoUrlAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "review" => false));
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}

		$colors = $em->getRepository('AppBundle:Color')->findAll();
		$tags = "";
		foreach ($wallpaper->getTagslist() as $key => $value) {
			if ($key == sizeof($wallpaper->getTagslist()) - 1) {
				$tags .= $value->getName();
			} else {
				$tags .= $value->getName() . ",";
			}
		}
		$wallpaper->setTags($tags);

		$wallpaperurl = $wallpaper->getVideo()->getUrl();
		$wallpaper->setUrlvideo($wallpaperurl);
		$form = $this->createForm(new VideoTypeUrl(), $wallpaper);
		$form->handleRequest($request);

		$file_ext = substr(strrchr($wallpaper->getUrlvideo(), '.'), 1);
		switch ($file_ext) {
		case 'mp4':
			$file_type = "video/mp4";
			break;
		case 'webm':
			$file_type = "video/webm";
			break;
		default:
			$file_type = "none";
			break;
		}

		if ($file_type != "none") {

			if ($form->isSubmitted() && $form->isValid()) {

				$wallpaper->setTagslist(array());
				$em->flush();

				$tags_list = explode(",", $wallpaper->getTags());
				foreach ($tags_list as $k => $v) {
					$tags_list[$k] = strtolower($v);
				}
				$tags_list = array_unique($tags_list);
				foreach ($tags_list as $key => $value) {
					$tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
					if ($tag == null) {
						$tag = new Tag();
						$tag->setName(strtolower($value));
						$em->persist($tag);
						$em->flush();
						$wallpaper->addTagslist($tag);
					} else {
						$wallpaper->addTagslist($tag);
					}
				}

				if ($wallpaperurl != $wallpaper->getUrlvideo()) {

					$wallpaper_media = new Media();
					$wallpaper_media->setTitre($wallpaper->getTitle());
					$wallpaper_media->setUrl($wallpaper->getUrlvideo());
					$wallpaper_media->setExtension($file_ext);
					$wallpaper_media->setType($file_type);
					$wallpaper_media->setEnabled(false);

					$wallpaper_media_old = $wallpaper->getVideo();
					$em->persist($wallpaper_media);
					$em->flush();

					$wallpaper->setVideo($wallpaper_media);
					$em->flush();

					$wallpaper_media_old->delete($this->container->getParameter('files_directory'));
					$em->remove($wallpaper_media_old);
					$em->flush();
				}
				if ($wallpaper->getFile() != null) {
					$media = new Media();
					$media_old = $wallpaper->getMedia();
					$media->setFile($wallpaper->getFile());
					$media->setEnabled(true);
					$media->upload($this->container->getParameter('files_directory'));
					$em->persist($media);
					$em->flush();
					$wallpaper->setMedia($media);
					$em->flush();
					$media_old->delete($this->container->getParameter('files_directory'));
					$em->remove($media_old);
					$em->flush();
				}

				$em->persist($wallpaper);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_wallpaper_index'));
			}
		} else {
			$type_error = new FormError("Url has video not supported");
			$form->get('urlvideo')->addError($type_error);
		}
		return $this->render("AppBundle:Wallpaper:video_edit_url.html.twig", array("colors" => $colors, "form" => $form->createView()));
	}

	public function addGifAction(Request $request) {
		$wallpaper = new Wallpaper();
		$wallpaper->setType("gif");
		$form = $this->createForm(new GifType(), $wallpaper);
		$em = $this->getDoctrine()->getManager();
		$colors = $em->getRepository('AppBundle:Color')->findAll();

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if ($wallpaper->getFilegif() != null) {

				$size = $this->formatNumber($wallpaper->getFilegif()->getClientSize());
				$resolution = $this->fileResolution($wallpaper->getFilegif());

				$media = new Media();
				$media->setFile($wallpaper->getFilegif());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();

				$path = $this->container->getParameter('files_directory') . $media->getPath();
				$color = $this->getWallpaperColor($path);

				$wallpaper->setUser($this->getUser());
				$wallpaper->setReview(false);
				$wallpaper->setDownloads(0);
				$wallpaper->setSize($size);
				$wallpaper->setColor($color);
				$wallpaper->setResolution($resolution);
				$wallpaper->setMedia($media);

				$tags_list = explode(",", $wallpaper->getTags());
				foreach ($tags_list as $key => $value) {
					$tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
					if ($tag == null) {
						$tag = new Tag();
						$tag->setName(strtolower($value));
						$em->persist($tag);
						$em->flush();
						$wallpaper->addTagslist($tag);
					} else {
						$wallpaper->addTagslist($tag);
					}
				}
				$em->persist($wallpaper);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_wallpaper_index'));
			} else {
				$photo_error = new FormError("Required image file");
				$form->get('filegif')->addError($photo_error);
			}
		}
		return $this->render("AppBundle:Wallpaper:gif_add.html.twig", array("colors" => $colors, "form" => $form->createView()));
	}
	public function editGifAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "type" => "gif", "review" => false));
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$colors = $em->getRepository('AppBundle:Color')->findAll();
		$tags = "";
		foreach ($wallpaper->getTagslist() as $key => $value) {
			if ($key == sizeof($wallpaper->getTagslist()) - 1) {
				$tags .= $value->getName();
			} else {
				$tags .= $value->getName() . ",";
			}
		}
		$wallpaper->setTags($tags);
		$form = $this->createForm(new GifType(), $wallpaper);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$wallpaper->setTagslist(array());
			$em->persist($wallpaper);
			$em->flush();

			$tags_list = explode(",", $wallpaper->getTags());
			foreach ($tags_list as $k => $v) {
				$tags_list[$k] = strtolower($v);
			}
			$tags_list = array_unique($tags_list);

			foreach ($tags_list as $key => $value) {
				$tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
				if ($tag == null) {
					$tag = new Tag();
					$tag->setName(strtolower($value));
					$em->persist($tag);
					$em->flush();
					$wallpaper->addTagslist($tag);
				} else {
					$wallpaper->addTagslist($tag);
				}
			}
			if ($wallpaper->getFilegif() != null) {

				$size = $this->formatNumber($wallpaper->getFilegif()->getClientSize());
				$resolution = $this->fileResolution($wallpaper->getFilegif());

				$media = new Media();
				$media_old = $wallpaper->getMedia();
				$media->setFile($wallpaper->getFilegif());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();

				$path = $this->container->getParameter('files_directory') . $media->getPath();
				$color = $this->getWallpaperColor($path);

				$wallpaper->setSize($size);
				$wallpaper->setColor($color);
				$wallpaper->setResolution($resolution);
				$wallpaper->setMedia($media);
				$em->flush();
				$media_old->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old);
				$em->flush();
			}
			$em->persist($wallpaper);
			$em->flush();
			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_wallpaper_index'));
		}
		return $this->render("AppBundle:Wallpaper:gif_edit.html.twig", array("colors" => $colors, "form" => $form->createView()));
	}

	public function addImageAction(Request $request) {
		$wallpaper = new Wallpaper();
		$wallpaper->setType("image");
		$form = $this->createForm(new ImageType(), $wallpaper);
		$em = $this->getDoctrine()->getManager();
		$colors = $em->getRepository('AppBundle:Color')->findAll();
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			if ($wallpaper->getFile() != null) {

				$size = $this->formatNumber($wallpaper->getFile()->getClientSize());
				$resolution = $this->fileResolution($wallpaper->getFile());

				$media = new Media();
				$media->setFile($wallpaper->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();

				$path = $this->container->getParameter('files_directory') . $media->getPath();
				$color = $this->getWallpaperColor($path);

				$wallpaper->setUser($this->getUser());
				$wallpaper->setReview(false);
				$wallpaper->setDownloads(0);
				$wallpaper->setSize($size);
				$wallpaper->setColor($color);
				$wallpaper->setResolution($resolution);
				$wallpaper->setMedia($media);

				$tags_list = explode(",", $wallpaper->getTags());
				foreach ($tags_list as $key => $value) {
					$tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
					if ($tag == null) {
						$tag = new Tag();
						$tag->setName(strtolower($value));
						$em->persist($tag);
						$em->flush();
						$wallpaper->addTagslist($tag);
					} else {
						$wallpaper->addTagslist($tag);
					}
				}

				$em->persist($wallpaper);
				$em->flush();
				$this->addFlash('success', 'Operation has been done successfully');
				return $this->redirect($this->generateUrl('app_wallpaper_index'));
			} else {
				$photo_error = new FormError("Required image file");
				$form->get('file')->addError($photo_error);
			}
		}
		return $this->render("AppBundle:Wallpaper:image_add.html.twig", array("colors" => $colors, "form" => $form->createView()));
	}
	public function editImageAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "review" => false));
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$colors = $em->getRepository('AppBundle:Color')->findAll();

		$tags = "";
		foreach ($wallpaper->getTagslist() as $key => $value) {
			if ($key == sizeof($wallpaper->getTagslist()) - 1) {
				$tags .= $value->getName();
			} else {
				$tags .= $value->getName() . ",";
			}
		}
		$wallpaper->setTags($tags);

		$form = $this->createForm(new ImageType(), $wallpaper);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {

			$wallpaper->setTagslist(array());
			$em->persist($wallpaper);
			$em->flush();

			$tags_list = explode(",", $wallpaper->getTags());
			foreach ($tags_list as $k => $v) {
				$tags_list[$k] = strtolower($v);
			}
			$tags_list = array_unique($tags_list);

			foreach ($tags_list as $key => $value) {
				$tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
				if ($tag == null) {
					$tag = new Tag();
					$tag->setName(strtolower($value));
					$em->persist($tag);
					$em->flush();
					$wallpaper->addTagslist($tag);
				} else {
					$wallpaper->addTagslist($tag);
				}
			}

			if ($wallpaper->getFile() != null) {
				$size = $this->formatNumber($wallpaper->getFile()->getClientSize());
				$resolution = $this->fileResolution($wallpaper->getFile());

				$media = new Media();
				$media_old = $wallpaper->getMedia();
				$media->setFile($wallpaper->getFile());
				$media->setEnabled(true);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();
				$wallpaper->setMedia($media);
				$em->flush();
				$media_old->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old);
				$em->flush();

				$path = $this->container->getParameter('files_directory') . $media->getPath();
				$color = $this->getWallpaperColor($path);

				$wallpaper->setSize($size);
				$wallpaper->setColor($color);
				$wallpaper->setResolution($resolution);
			}
			$em->persist($wallpaper);
			$em->flush();
			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_wallpaper_index'));
		}
		return $this->render("AppBundle:Wallpaper:image_edit.html.twig", array("colors" => $colors, "form" => $form->createView()));
	}
  public function api_add_setAction(Request $request, $token) {
    if ($token != $this->container->getParameter('token_app')) {
      throw new NotFoundHttpException("Page not found");
    }
    $em = $this->getDoctrine()->getManager();
    $id = $request->get("id");
    $wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "enabled" => true));
    if ($wallpaper == null) {
      throw new NotFoundHttpException("Page not found");
    }
    $wallpaper->setSets($wallpaper->getSets() + 1);
    $em->flush();
    $encoders = array(new XmlEncoder(), new JsonEncoder());
    $normalizers = array(new ObjectNormalizer());
    $serializer = new Serializer($normalizers, $encoders);
    $jsonContent = $serializer->serialize($wallpaper->getSets(), 'json');
    return new Response($jsonContent);
  }
  public function api_add_downloadAction(Request $request, $token) {
    if ($token != $this->container->getParameter('token_app')) {
      throw new NotFoundHttpException("Page not found");
    }
    $em = $this->getDoctrine()->getManager();
    $id = $request->get("id");
    $wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "enabled" => true));
    if ($wallpaper == null) {
      throw new NotFoundHttpException("Page not found");
    }
    $wallpaper->setDownloads($wallpaper->getDownloads() + 1);
    $em->flush();
    $encoders = array(new XmlEncoder(), new JsonEncoder());
    $normalizers = array(new ObjectNormalizer());
    $serializer = new Serializer($normalizers, $encoders);
    $jsonContent = $serializer->serialize($wallpaper->getDownloads(), 'json');
    return new Response($jsonContent);
  }
	public function api_add_shareAction(Request $request, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$id = $request->get("id");
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "enabled" => true));
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$wallpaper->setShares($wallpaper->getShares() + 1);
		$em->flush();
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($wallpaper->getShares(), 'json');
		return new Response($jsonContent);
	}
	public function api_add_viewAction(Request $request, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$id = $request->get("id");
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "enabled" => true));
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$wallpaper->setViews($wallpaper->getViews() + 1);
		$em->flush();
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($wallpaper->getViews(), 'json');
		return new Response($jsonContent);
	}

	public function api_add_woowAction(Request $request, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$id = $request->get("id");
		$em = $this->getDoctrine()->getManager();
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->find($id);
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$wallpaper->setWoow($wallpaper->getWoow() + 1);
		$em->flush();
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($wallpaper->getWoow(), 'json');
		return new Response($jsonContent);
	}

	public function api_allAction(Request $request, $page, $order, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');

		$query = $repository->createQueryBuilder('w')
			->where("w.enabled = true")
			->addOrderBy('w.' . $order, 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();

		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}

	public function api_by_randomAction(Request $request, $page, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');

		$query = $repository->createQueryBuilder('w')
			->leftJoin('w.categories', 'c')
			->where("w.enabled = true")
			->addSelect('RAND() as HIDDEN rand')
			->orderBy('rand')
			->setMaxResults($nombre)
			->getQuery();
		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}
	public function api_by_categoryAction(Request $request, $page, $category, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');

		$query = $repository->createQueryBuilder('w')
			->leftJoin('w.categories', 'c')
			->where('c.id = :category', "w.enabled = true")
			->setParameter('category', $category)
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();
		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}
	public function api_by_packAction(Request $request, $page, $pack, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');

		$query = $repository->createQueryBuilder('w')
			->leftJoin('w.packs', 'c')
			->where('c.id = :pack', "w.enabled = true")
			->setParameter('pack', $pack)
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();
		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}
	public function api_by_colorAction(Request $request, $page, $color, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');

		$query = $repository->createQueryBuilder('w')
			->leftJoin('w.colors', 'c')
			->where('c.id = :color', "w.enabled = true")
			->setParameter('color', $color)
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();
		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}
	public function api_by_followAction(Request $request, $page, $user, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');
		$query = $repository->createQueryBuilder('w')
			->leftJoin('w.user', 'u')
			->leftJoin('u.followers', 'f')
			->where('f.id = ' . $user, "w.enabled = true")
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();

		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}

	public function api_by_meAction(Request $request, $page, $user, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');
		$query = $repository->createQueryBuilder('w')
			->where('w.user = :user')
			->setParameter('user', $user)
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();
		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}

	public function api_by_queryAction(Request $request, $page, $query, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');

    $tags_list=explode(" ", $query);
    foreach ($tags_list as $key => $value) {
       $tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name"=>$value));
       if ($tag!=null) {
         $tag->setSearch($tag->getSearch()+1);
         $em->flush();           
       }
    }
		$repository = $em->getRepository('AppBundle:Wallpaper');
		$query_dql = $repository->createQueryBuilder('w')
			->where("w.enabled = true", "LOWER(w.title) like LOWER('%" . $query . "%') OR LOWER(w.tags) like LOWER('%" . $query . "%')  OR LOWER(w.description) like LOWER('%" . $query . "%') ")
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();

		$wallpapers = $query_dql->getResult();

		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}

	public function api_by_userAction(Request $request, $page, $user, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');
		$query = $repository->createQueryBuilder('w')
			->where('w.user = :user', "w.enabled = true")
			->setParameter('user', $user)
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();

		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}
	public function api_myAction(Request $request, $page, $user, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$nombre = 30;
		$em = $this->getDoctrine()->getManager();
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');
		$repository = $em->getRepository('AppBundle:Wallpaper');
		$query = $repository->createQueryBuilder('w')
			->leftJoin('w.user', 'c')
			->where('c.id = :user')
			->setParameter('user', $user)
			->addOrderBy('w.created', 'DESC')
			->addOrderBy('w.id', 'asc')
			->setFirstResult($nombre * $page)
			->setMaxResults($nombre)
			->getQuery();

		$wallpapers = $query->getResult();
		return $this->render('AppBundle:Wallpaper:api_all.html.php', array("wallpapers" => $wallpapers));
	}

	public function sendNotif($em, $selected_wallpaper) {
		$user = $selected_wallpaper->getUser();
		if ($user == null) {
			throw new NotFoundHttpException("Page not found");
		}
		$tokens = array();

		$tokens[] = $user->getToken();
		$original = "";
		$thumbnail = "";
		$type = "";
		$extension = "";
		$color = "";
		$imagineCacheManager = $this->get('liip_imagine.cache.manager');

    $original = "";
    $image = "";
    $thumbnail = "";
    $type = "";
    $extension = "";
    if ($selected_wallpaper->getVideo()) {
          $type=$selected_wallpaper->getVideo()->getType();
          $extension=$selected_wallpaper->getVideo()->getExtension();
    }else{
          $type=$selected_wallpaper->getMedia()->getType();
          $extension=$selected_wallpaper->getMedia()->getExtension();
    }
    $thumbnail= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_thumb_api');
    $image= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_image_api');
    if ($selected_wallpaper->getVideo()) {
          if ($selected_wallpaper->getVideo()->getEnabled()) {
                $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getVideo()->getLink()) ;
          }else{
                $original = $selected_wallpaper->getVideo()->getLink();
          } 
    }else{
                $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getMedia()->getLink()) ;
    }
		$message = array(
      "type"=> "wallpaper",
      "kind"=> $selected_wallpaper->getType(),
      "id"=> $selected_wallpaper->getId(),
      "wallpaper_kind"=>$selected_wallpaper->getType(),
      "wallpaper_title"=>$selected_wallpaper->getTitle(),
      "wallpaper_description"=>$selected_wallpaper->getDescription(),
      "wallpaper_review"=>$selected_wallpaper->getReview(),
      "wallpaper_premium"=>$selected_wallpaper->getPremium(),
      "wallpaper_color"=>$selected_wallpaper->getColor(),
      "wallpaper_size"=>$selected_wallpaper->getSize(),
      "wallpaper_resolution"=>$selected_wallpaper->getResolution(),
      "wallpaper_comment"=>$selected_wallpaper->getComment(),
      "wallpaper_comments"=>sizeof($selected_wallpaper->getComments()),
      "wallpaper_downloads"=>$selected_wallpaper->getDownloads(),
      "wallpaper_views"=>$selected_wallpaper->getViews(),
      "wallpaper_shares"=>$selected_wallpaper->getShares(),
      "wallpaper_sets"=>$selected_wallpaper->getSets(),
      "wallpaper_trusted"=>$selected_wallpaper->getUser()->getTrusted(),
      "wallpaper_user"=>$selected_wallpaper->getUser()->getName(),
      "wallpaper_userid"=>$selected_wallpaper->getUser()->getId(),
      "wallpaper_userimage"=>$selected_wallpaper->getUser()->getImage(),
      "wallpaper_type"=>$type,
      "wallpaper_extension"=>$extension,

      "wallpaper_thumbnail"=> $thumbnail,
      "wallpaper_image"=> $image,
      "wallpaper_original"=>$original,
      "wallpaper_created"=>$selected_wallpaper->getCreated()->format("y/m/d H:i"),
      "wallpaper_tags"=>$selected_wallpaper->getTags(),
			"title" => "👍👍 wallpaper approved ❤️❤️",
			"message" => "😀😀 Congratulation you wallpaper has been approved ❤️❤️",
			"image" => "",
			"icon" => "",
		);

		$setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());
		$key = $setting->getFirebasekey();
		$message_image = $this->send_notificationToken($tokens, $message, $key);
	}
	function send_notificationToken($tokens, $message, $key) {
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array(
			'registration_ids' => $tokens,
			'data' => $message,

		);
		$headers = array(
			'Authorization:key = ' . $key,
			'Content-Type: application/json',
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		if ($result === FALSE) {
			die('Curl failed: ' . curl_error($ch));
		}
		curl_close($ch);
		return $result;
	}
	public function api_uploadGifAction(Request $request, $token) {

		$id = str_replace('"', '', $request->get("id"));
		$key = str_replace('"', '', $request->get("key"));
		$title = str_replace('"', '', $request->get("title"));
		$description = str_replace('"', '', $request->get("description"));

		$colors_ids = str_replace('"', '', $request->get("colors"));
		$colors_list = explode("_", $colors_ids);

		$categories_ids = str_replace('"', '', $request->get("categories"));
		$categories_list = explode("_", $categories_ids);

		$code = "200";
		$message = "Ok";
		$values = array();
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('UserBundle:User')->findOneBy(array("id" => $id,"enabled"=>true));
		if ($user == null) {
			throw new NotFoundHttpException("Page not found");
		}
		if (sha1($user->getPassword()) != $key) {
			throw new NotFoundHttpException("Page not found");
		}
		if ($user) {

			if ($this->getRequest()->files->has('uploaded_file')) {
				// $old_media=$user->getMedia();
				$file_thum = $this->getRequest()->files->get('uploaded_file');

        $size = $this->formatNumber($file_thum->getClientSize());
        $resolution = $this->fileResolution($file_thum);

				$media_thum = new Media();
				$media_thum->setFile($file_thum);
				$media_thum->upload($this->container->getParameter('files_directory'));
				$em->persist($media_thum);
				$em->flush();


        $path = $this->container->getParameter('files_directory') . $media_thum->getPath();
        $color = $this->getWallpaperColor($path);

        $w = new Wallpaper();
        $w->setPremium(false);
        $w->setResolution($resolution);
        $w->setSize($size);
        $w->setColor($color);
				$w->setType("gif");
				$w->setDownloads(0);

				if (!$user->getTrusted()) {
					$w->setEnabled(false);
					$w->setReview(true);
				} else {
					$w->setEnabled(true);
					$w->setReview(false);
				}
				$w->setComment(true);
				$w->setTitle($title);
				$w->setDescription($description);
				$w->setUser($user);
				$w->setMedia($media_thum);

        foreach ($colors_list as $key => $id_color) {
          $color_obj = $em->getRepository('AppBundle:Color')->find($id_color);
          if ($color_obj) {
            $w->addColor($color_obj);
          }
        }
				foreach ($categories_list as $key => $id_category) {
					$category_obj = $em->getRepository('AppBundle:Category')->find($id_category);
					if ($category_obj) {
						$w->addCategory($category_obj);
					}
				}

				$em->persist($w);
				$em->flush();
			}
		}
		$error = array(
			"code" => $code,
			"message" => $message,
			"values" => $values,
		);
    if ($user->getTrusted()) {
        $this->sendNotif($em,$w);
    }
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($error, 'json');
		return new Response($jsonContent);
	}
	public function api_uploadAction(Request $request, $token) {

		$id = str_replace('"', '', $request->get("id"));
		$key = str_replace('"', '', $request->get("key"));
		$title = str_replace('"', '', $request->get("title"));
		$description = str_replace('"', '', $request->get("description"));

		$colors_ids = str_replace('"', '', $request->get("colors"));
		$colors_list = explode("_", $colors_ids);

		$categories_ids = str_replace('"', '', $request->get("categories"));
		$categories_list = explode("_", $categories_ids);

		$code = "200";
		$message = "Ok";
		$values = array();
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('UserBundle:User')->findOneBy(array("id" => $id,"enabled"=>true));
		if ($user == null) {
			throw new NotFoundHttpException("Page not found");
		}
		if (sha1($user->getPassword()) != $key) {
			throw new NotFoundHttpException("Page not found");
		}
		if ($user) {

			if ($this->getRequest()->files->has('uploaded_file')) {
				// $old_media=$user->getMedia();
				$file = $this->getRequest()->files->get('uploaded_file');
				$file_thum = $this->getRequest()->files->get('uploaded_file_thum');

				$size = $this->formatNumber($file->getClientSize());
				$resolution = $this->fileResolution($file_thum);

				$media = new Media();
				$media->setFile($file);
				$media->upload($this->container->getParameter('files_directory'));
				$em->persist($media);
				$em->flush();

				$media_thum = new Media();
				$media_thum->setFile($file_thum);
				$media_thum->upload($this->container->getParameter('files_directory'));
				$em->persist($media_thum);
				$em->flush();

				$path = $this->container->getParameter('files_directory') . $media_thum->getPath();
				$color = $this->getWallpaperColor($path);

				$w = new Wallpaper();
				$w->setType("video");
				$w->setDownloads(0);
				$w->setPremium(false);
				$w->setResolution($resolution);
				$w->setSize($size);
				$w->setColor($color);

				if (!$user->getTrusted()) {
					$w->setEnabled(false);
					$w->setReview(true);
				} else {
					$w->setEnabled(true);
					$w->setReview(false);
				}
				$w->setComment(true);
				$w->setTitle($title);
				$w->setDescription($description);
				$w->setUser($user);
				$w->setVideo($media);
				$w->setMedia($media_thum);

				foreach ($colors_list as $key => $id_color) {
					$color_obj = $em->getRepository('AppBundle:Color')->find($id_color);
					if ($color_obj) {
						$w->addColor($color_obj);
					}
				}
				foreach ($categories_list as $key => $id_category) {
					$category_obj = $em->getRepository('AppBundle:Category')->find($id_category);
					if ($category_obj) {
						$w->addCategory($category_obj);
					}
				}

				$em->persist($w);
				$em->flush();
			 if ($user->getTrusted()) {
            $this->sendNotif($em,$w);
        }
			}
		}
		$error = array(
			"code" => $code,
			"message" => $message,
			"values" => $values,
		);
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($error, 'json');
		return new Response($jsonContent);
	}

	public function api_uploadImageAction(Request $request, $token) {

		$id = str_replace('"', '', $request->get("id"));
		$key = str_replace('"', '', $request->get("key"));
		$title = str_replace('"', '', $request->get("title"));
		$description = str_replace('"', '', $request->get("description"));

		$colors_ids = str_replace('"', '', $request->get("colors"));
		$colors_list = explode("_", $colors_ids);

		$categories_ids = str_replace('"', '', $request->get("categories"));
		$categories_list = explode("_", $categories_ids);

		$code = "200";
		$message = "Ok";
		$values = array();
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('UserBundle:User')->findOneBy(array("id" => $id,"enabled"=>true));
		if ($user == null) {
			throw new NotFoundHttpException("Page not found");
		}
		if (sha1($user->getPassword()) != $key) {
			throw new NotFoundHttpException("Page not found");
		}
		if ($user) {

			if ($this->getRequest()->files->has('uploaded_file')) {
				// $old_media=$user->getMedia();
				$file_thum = $this->getRequest()->files->get('uploaded_file');

				$size = $this->formatNumber($file_thum->getClientSize());
				$resolution = $this->fileResolution($file_thum);

				$media_thum = new Media();
				$media_thum->setFile($file_thum);
				$media_thum->upload($this->container->getParameter('files_directory'));
				$em->persist($media_thum);
				$em->flush();

				$path = $this->container->getParameter('files_directory') . $media_thum->getPath();
				$color = $this->getWallpaperColor($path);

				$w = new Wallpaper();
				$w->setType("image");
				$w->setDownloads(0);
				$w->setDownloads(0);
				$w->setPremium(false);
				$w->setResolution($resolution);
				$w->setSize($size);
				$w->setColor($color);
				if (!$user->getTrusted()) {
					$w->setEnabled(false);
					$w->setReview(true);
				} else {
					$w->setEnabled(true);
					$w->setReview(false);
				}
				$w->setComment(true);
				$w->setTitle($title);
				$w->setDescription($description);
				$w->setUser($user);
				$w->setMedia($media_thum);

				foreach ($colors_list as $key => $id_color) {
					$color_obj = $em->getRepository('AppBundle:Color')->find($id_color);
					if ($color_obj) {
						$w->addColor($color_obj);
					}
				}
				foreach ($categories_list as $key => $id_category) {
					$category_obj = $em->getRepository('AppBundle:Category')->find($id_category);
					if ($category_obj) {
						$w->addCategory($category_obj);
					}
				}

				$em->persist($w);
				$em->flush();

			 if ($user->getTrusted()) {
            $this->sendNotif($em,$w);
        }
			}
		}
		$error = array(
			"code" => $code,
			"message" => $message,
			"values" => $values,
		);
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($error, 'json');
		return new Response($jsonContent);
	}

	public function deleteAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->find($id);
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}

		$form = $this->createFormBuilder(array('id' => $id))
			->add('id', 'hidden')
			->add('Yes', 'submit')
			->getForm();
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$media_old_video = null;
			$media_old_thumb = null;
			if ($wallpaper->getVideo() != null) {
				$media_old_video = $wallpaper->getVideo();
			}
			if ($wallpaper->getMedia() != null) {
				$media_old_thumb = $wallpaper->getMedia();
			}
			$em->remove($wallpaper);
			$em->flush();
			if ($media_old_thumb != null) {
				$media_old_thumb->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old_thumb);
				$em->flush();
			}
			if ($media_old_video != null) {
				$media_old_video->delete($this->container->getParameter('files_directory'));
				$em->remove($media_old_video);
				$em->flush();
			}
			$this->addFlash('success', 'Operation has been done successfully');
			return $this->redirect($this->generateUrl('app_wallpaper_index'));
		}
		return $this->render('AppBundle:Wallpaper:delete.html.twig', array("form" => $form->createView()));
	}

	public function indexAction(Request $request) {

		$em = $this->getDoctrine()->getManager();
		$q = "  ";
		if ($request->query->has("q") and $request->query->get("q") != "") {
			$q .= " AND  w.title like '%" . $request->query->get("q") . "%'";
		}

		$dql = "SELECT i FROM AppBundle:Wallpaper i  WHERE i.review = false " . $q . " ORDER BY i.created desc ";
		$query = $em->createQuery($dql);
		$paginator = $this->get('knp_paginator');
		$wallpapers = $paginator->paginate(
			$query,
			$request->query->getInt('page', 1),
			12
		);
		$wallpaper_count = $em->getRepository('AppBundle:Wallpaper')->countAll();
		return $this->render('AppBundle:Wallpaper:index.html.twig', array("wallpapers" => $wallpapers, "wallpaper_count" => $wallpaper_count));
	}

	public function reviewAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->findOneBy(array("id" => $id, "review" => true));
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}

    $tags = "";
    foreach ($wallpaper->getTagslist() as $key => $value) {
      if ($key == sizeof($wallpaper->getTagslist()) - 1) {
        $tags .= $value->getName();
      } else {
        $tags .= $value->getName() . ",";
      }
    }
    $wallpaper->setTags($tags);
    $colors = $em->getRepository('AppBundle:Color')->findAll();

		$form = $this->createForm(new WallpaperReviewType(), $wallpaper);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {

      $wallpaper->setTagslist(array());
      $em->persist($wallpaper);
      $em->flush();

      $tags_list = explode(",", $wallpaper->getTags());
      foreach ($tags_list as $k => $v) {
        $tags_list[$k] = strtolower($v);
      }
      $tags_list = array_unique($tags_list);

      foreach ($tags_list as $key => $value) {
        $tag = $em->getRepository("AppBundle:Tag")->findOneBy(array("name" => strtolower($value)));
        if ($tag == null) {
          $tag = new Tag();
          $tag->setName(strtolower($value));
          $em->persist($tag);
          $em->flush();
          $wallpaper->addTagslist($tag);
        } else {
          $wallpaper->addTagslist($tag);
        }
      }

			$wallpaper->setReview(false);
			$wallpaper->setEnabled(true);
			$wallpaper->setCreated(new \DateTime());
			$em->persist($wallpaper);
			$em->flush();
			$this->addFlash('success', 'Operation has been done successfully');

			return $this->redirect($this->generateUrl('app_home_notif_user_wallpaper', array("wallpaper_id" => $wallpaper->getId())));
		}
		return $this->render("AppBundle:Wallpaper:review.html.twig", array("colors"=>$colors,"form" => $form->createView()));
	}

	public function reviewsAction(Request $request) {

		$em = $this->getDoctrine()->getManager();

		$dql = "SELECT w FROM AppBundle:Wallpaper w  WHERE w.review = true ORDER BY w.created desc ";
		$query = $em->createQuery($dql);
		$paginator = $this->get('knp_paginator');
		$wallpapers = $paginator->paginate(
			$query,
			$request->query->getInt('page', 1),
			12
		);
		$wallpaper_list = $em->getRepository('AppBundle:Wallpaper')->findBy(array("review" => true));
		$wallpapers_count = sizeof($wallpaper_list);

		return $this->render('AppBundle:Wallpaper:reviews.html.twig', array("wallpapers" => $wallpapers, "wallpapers_count" => $wallpapers_count));
	}

	public function viewAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();
		$wallpaper = $em->getRepository("AppBundle:Wallpaper")->find($id);
		if ($wallpaper == null) {
			throw new NotFoundHttpException("Page not found");
		}
        $rates_1 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper"=>$wallpaper,"value"=>1));
        $rates_2 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper"=>$wallpaper,"value"=>2));
        $rates_3 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper"=>$wallpaper,"value"=>3));
        $rates_4 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper"=>$wallpaper,"value"=>4));
        $rates_5 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper"=>$wallpaper,"value"=>5));
        $rates = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper"=>$wallpaper));


        $ratings["rate_1"]=sizeof($rates_1);
        $ratings["rate_2"]=sizeof($rates_2);
        $ratings["rate_3"]=sizeof($rates_3);
        $ratings["rate_4"]=sizeof($rates_4);
        $ratings["rate_5"]=sizeof($rates_5);


        $t = sizeof($rates_1) + sizeof($rates_2) +sizeof($rates_3)+ sizeof($rates_4) + sizeof($rates_5);
        if ($t == 0) {
            $t=1;
        }
        $values["rate_1"]=(sizeof($rates_1)*100)/$t;
        $values["rate_2"]=(sizeof($rates_2)*100)/$t;
        $values["rate_3"]=(sizeof($rates_3)*100)/$t;
        $values["rate_4"]=(sizeof($rates_4)*100)/$t;
        $values["rate_5"]=(sizeof($rates_5)*100)/$t;

        $total=0;
        $count=0;
        foreach ($rates as $key => $r) {
           $total+=$r->getValue();
           $count++;
        }
        $v=0;
        if ($count != 0) {
            $v=$total/$count;
        }
        $rating=$v;
        return $this->render("AppBundle:Wallpaper:view.html.twig",array("wallpaper"=>$wallpaper,"rating"=>$rating,"ratings"=>$ratings,"values"=>$values));
	}
	public function api_add_rateAction($user, $wallpaper, $value, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$a = $em->getRepository('AppBundle:Wallpaper')->find($wallpaper);
		$u = $em->getRepository("UserBundle:User")->find($user);
		$code = "200";
		$message = "";
		$errors = array();

		if ($u != null and $a != null) {
			$rate = $em->getRepository('AppBundle:Rate')->findOneBy(array("user" => $u, "wallpaper" => $a));
			if ($rate == null) {
  				$rate_obj = new Rate();
  				$rate_obj->setValue($value);
  				$rate_obj->setWallpaper($a);
  				$rate_obj->setUser($u);
  				$em->persist($rate_obj);
  				$em->flush();
  				$message = "Your Ratting has been added";
  				if ($u->getId() != $a->getUser()->getId()) {
  					$stars = "";
  					for ($i = 0; $i < $value; $i++) {
  						$stars .= "★";
  					}
            $tokens[] = $a->getUser()->getToken();
            $imagineCacheManager = $this->get('liip_imagine.cache.manager');
            $selected_wallpaper = $a;
            $original = "";
            $image = "";
            $thumbnail = "";
            $type = "";
            $extension = "";
            if ($selected_wallpaper->getVideo()) {
                  $type=$selected_wallpaper->getVideo()->getType();
                  $extension=$selected_wallpaper->getVideo()->getExtension();
            }else{
                  $type=$selected_wallpaper->getMedia()->getType();
                  $extension=$selected_wallpaper->getMedia()->getExtension();
            }
            $thumbnail= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_thumb_api');
            $image= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_image_api');
            if ($selected_wallpaper->getVideo()) {
                  if ($selected_wallpaper->getVideo()->getEnabled()) {
                        $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getVideo()->getLink()) ;
                  }else{
                        $original = $selected_wallpaper->getVideo()->getLink();
                  } 
            }else{
                        $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getMedia()->getLink()) ;
            }
            $messageNotif = array(
                  "type"=> "wallpaper",
                  "kind"=> $selected_wallpaper->getType(),
                  "id"=> $selected_wallpaper->getId(),
                  "wallpaper_kind"=>$selected_wallpaper->getType(),
                  "wallpaper_title"=>$selected_wallpaper->getTitle(),
                  "wallpaper_description"=>$selected_wallpaper->getDescription(),
                  "wallpaper_review"=>$selected_wallpaper->getReview(),
                  "wallpaper_premium"=>$selected_wallpaper->getPremium(),
                  "wallpaper_color"=>$selected_wallpaper->getColor(),
                  "wallpaper_size"=>$selected_wallpaper->getSize(),
                  "wallpaper_resolution"=>$selected_wallpaper->getResolution(),
                  "wallpaper_comment"=>$selected_wallpaper->getComment(),
                  "wallpaper_comments"=>sizeof($selected_wallpaper->getComments()),
                  "wallpaper_downloads"=>$selected_wallpaper->getDownloads(),
                  "wallpaper_views"=>$selected_wallpaper->getViews(),
                  "wallpaper_shares"=>$selected_wallpaper->getShares(),
                  "wallpaper_sets"=>$selected_wallpaper->getSets(),
                  "wallpaper_trusted"=>$selected_wallpaper->getUser()->getTrusted(),
                  "wallpaper_user"=>$selected_wallpaper->getUser()->getName(),
                  "wallpaper_userid"=>$selected_wallpaper->getUser()->getId(),
                  "wallpaper_userimage"=>$selected_wallpaper->getUser()->getImage(),
                  "wallpaper_type"=>$type,
                  "wallpaper_extension"=>$extension,

                  "wallpaper_thumbnail"=> $thumbnail,
                  "wallpaper_image"=> $image,
                  "wallpaper_original"=>$original,
                  "wallpaper_created"=>$selected_wallpaper->getCreated()->format("y/m/d H:i"),
                  "wallpaper_tags"=>$selected_wallpaper->getTags(),
                  "title" => $u->getName() . " Rate " . $a->getTitle(),
                  "message" => $value . " " . $stars . " Rating"
                );

             $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
             $key=$setting->getFirebasekey();
             $message_status = $this->send_notificationToken($tokens, $messageNotif, $key);
  				}
			} else {
				$rate->setValue($value);
				$em->flush();
				$message = "Your Ratting has been edit";
				if ($u->getId() != $a->getUser()->getId()) {
					$stars = "";
					for ($i = 0; $i < $value; $i++) {
						$stars .= "★";
					}
					  $tokens[] = $a->getUser()->getToken();
            $imagineCacheManager = $this->get('liip_imagine.cache.manager');
            $selected_wallpaper = $a;
            $original = "";
            $image = "";
            $thumbnail = "";
            $type = "";
            $extension = "";
            if ($selected_wallpaper->getVideo()) {
                  $type=$selected_wallpaper->getVideo()->getType();
                  $extension=$selected_wallpaper->getVideo()->getExtension();
            }else{
                  $type=$selected_wallpaper->getMedia()->getType();
                  $extension=$selected_wallpaper->getMedia()->getExtension();
            }
            $thumbnail= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_thumb_api');
            $image= $imagineCacheManager->getBrowserPath($selected_wallpaper->getMedia()->getLink(), 'wallpaper_image_api');
            if ($selected_wallpaper->getVideo()) {
                  if ($selected_wallpaper->getVideo()->getEnabled()) {
                        $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getVideo()->getLink()) ;
                  }else{
                        $original = $selected_wallpaper->getVideo()->getLink();
                  } 
            }else{
                        $original = $this->getRequest()->getUriForPath("/".$selected_wallpaper->getMedia()->getLink()) ;
            }
            $messageNotif = array(
                  "type"=> "wallpaper",
                  "kind"=> $selected_wallpaper->getType(),
                  "id"=> $selected_wallpaper->getId(),
                  "wallpaper_kind"=>$selected_wallpaper->getType(),
                  "wallpaper_title"=>$selected_wallpaper->getTitle(),
                  "wallpaper_description"=>$selected_wallpaper->getDescription(),
                  "wallpaper_review"=>$selected_wallpaper->getReview(),
                  "wallpaper_premium"=>$selected_wallpaper->getPremium(),
                  "wallpaper_color"=>$selected_wallpaper->getColor(),
                  "wallpaper_size"=>$selected_wallpaper->getSize(),
                  "wallpaper_resolution"=>$selected_wallpaper->getResolution(),
                  "wallpaper_comment"=>$selected_wallpaper->getComment(),
                  "wallpaper_comments"=>sizeof($selected_wallpaper->getComments()),
                  "wallpaper_downloads"=>$selected_wallpaper->getDownloads(),
                  "wallpaper_views"=>$selected_wallpaper->getViews(),
                  "wallpaper_shares"=>$selected_wallpaper->getShares(),
                  "wallpaper_sets"=>$selected_wallpaper->getSets(),
                  "wallpaper_trusted"=>$selected_wallpaper->getUser()->getTrusted(),
                  "wallpaper_user"=>$selected_wallpaper->getUser()->getName(),
                  "wallpaper_userid"=>$selected_wallpaper->getUser()->getId(),
                  "wallpaper_userimage"=>$selected_wallpaper->getUser()->getImage(),
                  "wallpaper_type"=>$type,
                  "wallpaper_extension"=>$extension,

                  "wallpaper_thumbnail"=> $thumbnail,
                  "wallpaper_image"=> $image,
                  "wallpaper_original"=>$original,
                  "wallpaper_created"=>$selected_wallpaper->getCreated()->format("y/m/d H:i"),
                  "wallpaper_tags"=>$selected_wallpaper->getTags(),
                  "title" => $u->getName() . " Edit Rate " . $a->getTitle(),
                  "message" => $value . " " . $stars . " Rating"
                );

             $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
             $key=$setting->getFirebasekey();
					   $message_status = $this->send_notificationToken($tokens, $messageNotif, $key);
				}
			}
		} else {
			$code = "500";
			$message = "Sorry, your rate could not be added at this time";
		}
		$error = array(
			"code" => $code,
			"message" => $message,
			"values" => $errors,
		);
    sleep(1);
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($error, 'json');
		return new Response($jsonContent);
	}
	public function api_get_rateAction($user = null, $wallpaper, $token) {
		if ($token != $this->container->getParameter('token_app')) {
			throw new NotFoundHttpException("Page not found");
		}
		$em = $this->getDoctrine()->getManager();
		$a = $em->getRepository('AppBundle:Wallpaper')->find($wallpaper);
		$u = $em->getRepository("UserBundle:User")->find($user);
		$code = "200";
		$message = "";
		$errors = array();

		if ($a != null) {
			$rates_1 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper" => $a, "value" => 1));
			$rates_2 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper" => $a, "value" => 2));
			$rates_3 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper" => $a, "value" => 3));
			$rates_4 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper" => $a, "value" => 4));
			$rates_5 = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper" => $a, "value" => 5));
			$rates = $em->getRepository('AppBundle:Rate')->findBy(array("wallpaper" => $a));
			$rate = null;
			if ($u != null) {
				$rate = $em->getRepository('AppBundle:Rate')->findOneBy(array("user" => $u, "wallpaper" => $a));
			}
			if ($rate == null) {
				$code = "202";
			} else {
				$message = $rate->getValue();
			}

			$errors[] = array("name" => "1", "value" => sizeof($rates_1));
			$errors[] = array("name" => "2", "value" => sizeof($rates_2));
			$errors[] = array("name" => "3", "value" => sizeof($rates_3));
			$errors[] = array("name" => "4", "value" => sizeof($rates_4));
			$errors[] = array("name" => "5", "value" => sizeof($rates_5));
			$total = 0;
			$count = 0;
			foreach ($rates as $key => $r) {
				$total += $r->getValue();
				$count++;
			}
			$v = 0;
			if ($count != 0) {
				$v = $total / $count;
			}
			$v2 = number_format((float) $v, 1, '.', '');
			$errors[] = array("name" => "rate", "value" => $v2);

		} else {
			$code = "500";
			$message = "Sorry, your rate could not be added at this time";
		}
		$error = array(
			"code" => $code,
			"message" => $message,
			"values" => $errors,
		);
		$encoders = array(new XmlEncoder(), new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$jsonContent = $serializer->serialize($error, 'json');
		return new Response($jsonContent);
	}
}
?>